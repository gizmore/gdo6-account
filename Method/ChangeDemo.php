<?php
namespace GDO\Account\Method;

use GDO\Account\GDO_AccountChange;
use GDO\Account\Module_Account;
use GDO\Core\Method;
use GDO\Country\GDO_Country;
use GDO\User\GDO_User;
use GDO\Util\Common;
use GDO\Date\Time;
use GDO\Language\GDO_Language;
use GDO\Mail\Mail;
use GDO\UI\GDT_Link;
use GDO\Core\GDT_Hook;
use GDO\DB\GDT_String;
use GDO\Core\Application;
/**
 * Demographic chance only once in a while.
 * 
 * @author gizmore
 * @version 6.10
 */
final class ChangeDemo extends Method
{
	public function isAlwaysTransactional() { return true; }
	
	public function gdoParameters()
	{
		return array(
			GDT_String::make('token')->notNull(),
		);
	}
	
	public function execute()
	{
		if ($token = Common::getGetString('token'))
		{
			return $this->onChange($token);
		}
	}
	
	public static function requestChange(Module_Account $module, GDO_User $user, array $data)
	{
		if (true !== ($error = self::mayChange($module, $user)))
		{
			return $error;
		}
		
		if ($module->cfgDemoMail() && $user->hasMail())
		{
			return self::sendMail($module, $user, $data);
		}
		else
		{
			return self::change($module, $user, $data);
		}
	}
	
	private static function mayChange(Module_Account $module, GDO_User $user)
	{
		if ($row = GDO_AccountChange::getRow($user->getID(), 'demo_lock'))
		{
			$last = $row->getTimestamp();
			$elapsed = Application::$TIME - $last;
			$min_wait = $module->cfgChangeTime();
			if ($elapsed < $min_wait)
			{
				$wait = $min_wait - $elapsed;
				return $module->error('err_demo_wait', array(Time::humanDuration($wait)));
			}
		}
		return true;
	}
	
	public static function change(Module_Account $module, GDO_User $user, array $data)
	{
		$user->saveVars($data);
		GDT_Hook::callWithIPC('AccountChanged', $user);
		GDO_AccountChange::addRow($user->getID(), 'demo_lock');
		return $module->message('msg_demo_changed');
	}
	
	private static function sendMail(Module_Account $module, GDO_User $user, array $data)
	{
		$ac = GDO_AccountChange::addRow($user->getID(), 'demo', $data);
		$username = $user->displayName();
		$sitename = sitename();
		$timeout = Time::humanDuration($module->cfgChangeTime());
		$gender = t('enum_'.$data['user_gender']);
		$country = GDO_Country::getByISOOrUnknown($data['user_country'])->displayName();
		$language = GDO_Language::getByISOOrUnknown($data['user_language'])->displayName();
// 		$birthdate = $data['user_birthdate'] > 0 ? Time::displayDate($data['user_birthdate'], 'day') : t('unknown');
		$link = GDT_Link::anchor(url('Account', 'ChangeDemo', sprintf("&userid=%d&token=%s", $user->getID(), $ac->getToken())));
		$args = [$username, $sitename, $timeout, $country, $language, $gender, $link];

		$mail = new Mail();
		$mail->setSender(GDO_BOT_EMAIL);
		$mail->setSenderName(GDO_BOT_NAME);
		$mail->setSubject(t('mail_subj_demochange', [$sitename]));
		$mail->setBody(t('mail_body_demochange', $args));
		$mail->sendToUser($user);
		return $module->message('msg_mail_sent');
	}
	
	private function onChange($token)
	{
		$userid = Common::getGetString('userid');
		if (!($ac = GDO_AccountChange::getRow($userid, 'demo', $token)))
		{
			return $this->error('err_token');
		}
		if (!($user = GDO_User::getById($userid)))
		{
			return $this->error('err_user');
		}
		
		$data = $ac->getData();
		$user->saveVars($data);
		$ac->delete();

		GDO_AccountChange::addRow($userid, 'demo_lock');
		GDT_Hook::callWithIPC('AccountChanged', $user);
		
		return $this->message('msg_demo_changed');
	}
}
