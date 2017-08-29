<?php
namespace GDO\Account\Method;

use GDO\Account\AccountAccess;
use GDO\Account\Module_Account;
use GDO\Table\GDT_Count;
use GDO\Table\MethodQueryTable;
use GDO\User\User;
/**
 * Tabular overview of old logins.
 * @author gizmore
 */
final class Access extends MethodQueryTable
{
	public function getUserType() { return User::MEMBER; }
	
	public function isEnabled() { return Module_Account::instance()->cfgFeatureAccess(); }
	
	public function execute()
	{
		return Module_Account::instance()->renderAccountTabs()->add(parent::execute());
	}
	
	public function getQuery()
	{
		return AccountAccess::table()->select('*')->where('accacc_uid='.User::current()->getID());
	}
	
	public function getHeaders()
	{
		$headers = array(
			GDT_Count::make(),
		);
		return array_merge($headers, AccountAccess::table()->getGDOColumns(['accacc_time', 'accacc_ip']));
	}
	
}
