<?php
namespace GDO\Account\Method;

use GDO\Account\Module_Account;
use GDO\Core\Method;
use GDO\Core\MethodAdmin;

final class Admin extends Method
{
    use MethodAdmin;
    
	public function getPermission() { return 'staff'; }
	
	public function beforeExecute()
	{
	    Module_Account::instance()->renderAdminTabs();
	}
	
	public function execute()
	{
		return $this->templatePHP('admin.php');
	}
	
}
