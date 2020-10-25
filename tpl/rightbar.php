<?php
use GDO\Account\Module_Account;
use GDO\UI\GDT_Bar;
use GDO\UI\GDT_Link;
use GDO\User\GDO_User;
/** @var $navbar GDT_Bar **/
?>
<?php
$user = GDO_User::current();
if ( ($user->isMember()) ||
	 ($user->isGuest() && Module_Account::instance()->cfgAllowGuests()) )
{
	$navbar->addField(GDT_Link::make('btn_account')->href(href('Account', 'Settings')));
}
