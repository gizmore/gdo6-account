<?php
namespace GDO\Account\Method;

use GDO\Account\GDO_AccountAccess;
use GDO\Account\Module_Account;
use GDO\Table\GDT_Count;
use GDO\Table\MethodQueryTable;
use GDO\User\GDO_User;

/**
 * Tabular overview of old logins.
 * @author gizmore
 */
final class Access extends MethodQueryTable
{
	public function getUserType() { return GDO_User::MEMBER; }
	
	public function isEnabled() { return Module_Account::instance()->cfgFeatureAccess(); }
	
	public function gdoTable() { return GDO_AccountAccess::table(); }
	
	public function execute()
	{
		Module_Account::instance()->renderAccountTabs();
		return parent::execute();
	}
	
	public function getQuery()
	{
		return GDO_AccountAccess::table()->select('*')->where('accacc_uid='.GDO_User::current()->getID());
	}
	
	public function gdoHeaders()
	{
		$headers = array(
			GDT_Count::make(),
		);
		return array_merge($headers, GDO_AccountAccess::table()->getGDOColumns(['accacc_time', 'accacc_ip']));
	}
	
}
