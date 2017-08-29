<?php
namespace GDO\Account;
use GDO\DB\GDO;
use GDO\Type\GDT_Checkbox;
use GDO\User\GDT_User;
use GDO\User\User;
/**
 * UserSettings table for the account module.
 * 
 *  - record IPs
 *  - alertion on suspicous account activity
 *  
 * @author gizmore
 * @version 5.0
 */
class AccountSetting extends GDO
{
	public function gdoCached() { return false; }
	
	public function gdoColumns()
	{
		return array(
			GDT_User::make('accset_user')->primary(),
			# Security
			GDT_Checkbox::make('accset_record_ip')->initial('0'),
			GDT_Checkbox::make('accset_uawatch')->initial('0'),
			GDT_Checkbox::make('accset_ipwatch')->initial('0'),
			GDT_Checkbox::make('accset_ispwatch')->initial('0'),
			# ???
		);
	}
	
	public function recordIPs() { return $this->getVar('accset_record_ip') === '1'; }
	public function alertOnUserAgent() { return $this->getVar('accset_uawatch') === '1'; }
	public function alertOnIPChange() { return $this->getVar('accset_ipwatch') === '1'; }
	public function alertOnISPChange() { return $this->getVar('accset_ispwatch') === '1'; }

	/**
	 * Get settings for a user.
	 * @param User $user
	 * @return AccountSetting
	 */
	public static function forUser(User $user)
	{
		if (!($setting = self::table()->find($user->getID(), false)))
		{
			$setting = self::blank(['accset_user' => $user->getID()]);
		}
		return $setting;
	}
	
}
