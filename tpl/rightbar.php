<?php
use GDO\Account\Module_Account;
use GDO\Template\GDO_Bar;
use GDO\UI\GDO_Link;
use GDO\User\User;
$navbar instanceof GDO_Bar;
?>
<?php
$user = User::current();
if ( ($user->isMember()) ||
	 ($user->isGuest() && Module_Account::instance()->cfgAllowGuests()) )
{
	$navbar->addField(GDO_Link::make('btn_account')->href(href('Account', 'Form')));
}
