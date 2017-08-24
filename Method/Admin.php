<?php
namespace GDO\Account\Method;

use GDO\Account\Module_Account;
use GDO\Core\Method;

final class Admin extends Method
{
	public function getPermission()
	{
		return 'staff';
	}
	
	public function execute()
	{
		return Module_Account::instance()->onRenderAdminTabs()->add($this->renderPage());
	}
	
	public function renderPage()
	{
		return $this->templatePHP('admin.php');
	}
	
}
