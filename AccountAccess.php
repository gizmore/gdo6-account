<?php
namespace GDO\Account;

use GDO\Core\Application;
use GDO\DB\GDO;
use GDO\DB\GDT_AutoInc;
use GDO\DB\GDT_CreatedAt;
use GDO\Mail\Mail;
use GDO\Net\GDT_IP;
use GDO\Type\GDT_MD5;
use GDO\UI\GDT_Link;
use GDO\User\GDT_User;
use GDO\User\User;
/**
 * Table with user login history.
 * Alerts user on suspicous change of IP / InternetServiceProvider / UserAgent
 * 
 * @author gizmore
 * @version 5.0
 * @since 3.0
 * 
 * @see User
 * @see AccountSetting
 */
final class AccountAccess extends GDO
{
	public function gdoCached() { return false; }
	
	###########
	### GDO ###
	###########
	public function gdoColumns()
	{
		return array(
			GDT_AutoInc::make('accacc_id'),
			GDT_User::make('accacc_uid')->index(),
			GDT_MD5::make('accacc_ua')->notNull(),
			GDT_IP::make('accacc_ip')->notNull(),
			GDT_MD5::make('accacc_isp'),
			GDT_CreatedAt::make('accacc_time'),
		);
	}

	/**
	 * On authentication, check the old history against current data.
	 * Mail on suspicous activity.
	 * Add a new entry.
	 * @param Module_Account $module
	 * @param User $user
	 */
	public static function onAccess(Module_Account $module, User $user)
	{
		$setting = AccountSetting::forUser($user);
		
		$query = '';
		
		# Check UA
		$ua = self::uahash();
		if ($setting->alertOnUserAgent())
		{
			$query .= " AND ".self::hash_check('accacc_ua', $ua);
		}
		
		# Check exact IP
		$ip = GDT_IP::current();
		if ($setting->alertOnIPChange())
		{
			$query .= " AND accacc_ip=".GDO::quoteS($ip);
		}
		
		# Check ISP
		$isp = null;
		if ($setting->alertOnISPChange())
		{
			$isp = self::isphash();
			$query .= ' AND '.self::hash_check('accacc_isp', $isp);
		}
		
		# Query alert
		if (!empty($query))
		{
			if (0 != self::table()->select('COUNT(*)')->where("accacc_uid={$user->getID()}")->exec()->fetchValue())
			{
				if (!self::table()->select('1')->where("accacc_uid={$user->getID()} $query")->exec()->fetchValue())
				{
					self::sendAlertMail($module, $user);
				}
			}
		}
		
		if ($setting->recordIPs())
		{
			# New access insert
			self::blank(array(
				'accacc_uid' => $user->getID(),
				'accacc_ua' => $ua,
				'accacc_ip' => $ip,
				'accacc_isp' => $isp,
			))->insert();
		}
	}
	
	private static function isphash()
	{
		if (GDT_IP::current() === ($isp = @gethostbyaddr($_SERVER['REMOTE_ADDR'])))
		{
			$isp = null;
		}
		return self::hash($isp);
	}
	
	private static function uahash()
	{
		return self::hash(preg_replace('/\d/', '', $_SERVER['HTTP_USER_AGENT']));
	}
	
	private static function hash_check($field, $hash, $quote='"')
	{
		return $hash === null ? $field.' IS NULL' : $field.'='.quote($hash);
	}
	
	private static function hash($value)
	{
		return $value === null ? null : md5($value, true);
	}
	
	public static function sendAlertMail(Module_Account $module, User $user, string $append='')
	{
		if ($receive_mail = $user->getMail())
		{
			$mail = new Mail();
			$mail->setSender(GWF_BOT_EMAIL);
			$mail->setSenderName(GWF_BOT_NAME);
			$mail->setReceiver($receive_mail);
			$mail->setSubject(t("mail_subj_account_alert$append", [sitename()]));
			$mail->setBody(t("mail_body_account_alert$append", array(
				$user->displayName(),
				sitename(),
				html($_SERVER['HTTP_USER_AGENT']),
				$_SERVER['REMOTE_ADDR'],
				gethostbyaddr($_SERVER['REMOTE_ADDR']),
				GDT_Link::anchor(url('Account', 'Access')),
				GDT_Link::anchor(url('Account', 'Form')),
			)));
			$mail->sendToUser($user);
		}
	}
	
}
