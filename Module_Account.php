<?php
namespace GDO\Account;

use GDO\Core\Application;
use GDO\Core\GDO_Module;
use GDO\Date\GDT_Duration;
use GDO\DB\GDT_Checkbox;
use GDO\DB\GDT_Int;
use GDO\User\GDO_User;
use GDO\Core\GDT_Template;
use GDO\UI\GDT_Page;
use GDO\UI\GDT_Link;
use GDO\Core\Module_Core;

/**
 * Member Account Changes.
 * 
 * @see GDO_User
 * 
 * @author gizmore
 * @version 6.10.2
 * @since 3.0.0
 */
final class Module_Account extends GDO_Module
{
    public $module_priority = 20;
    
    ##############
	### Module ###
	##############
	public function onLoadLanguage() { return $this->loadLanguage('lang/account'); }
	public function getClasses()
	{
	    return [
	        'GDO\Account\GDO_AccountAccess',
	        'GDO\Account\GDO_AccountChange',
	        'GDO\Account\GDO_AccountDelete',
	        'GDO\Account\GDO_AccountSetting'
	    ];
	}

	##############
	### Config ###
	##############
	public function getConfig()
	{
		return [
			GDT_Int::make('adult_age')->unsigned()->min(12)->max(40)->initial('21'),
			GDT_Duration::make('account_changetime')->min(0)->initial('90d'),
			GDT_Checkbox::make('allow_real_name')->initial('1'),
			GDT_Checkbox::make('allow_guest_settings')->initial('1'),
			GDT_Checkbox::make('allow_country_change')->initial('1'),
			GDT_Checkbox::make('allow_lang_change')->initial('1'),
			GDT_Checkbox::make('allow_gender_change')->initial('1'),
			GDT_Checkbox::make('allow_email_change')->initial('1'),
			GDT_Checkbox::make('feature_access_history')->initial('1'),
			GDT_Checkbox::make('feature_account_deletion')->initial('1'),
			GDT_Checkbox::make('feature_demographic_mail_confirm')->initial('1'),
		    GDT_Checkbox::make('hook_right_bar')->initial('1'),
		];
	}
	
	#############
	### Hooks ###
	#############
	public function hookUserAuthenticated(GDO_User $user)
	{
		if (!Application::instance()->isCLI())
		{
			GDO_AccountAccess::onAccess($this, $user);
		}
	}

	##################
	### Convinient ###
	##################
	public function cfgDemoMail() { return $this->getConfigValue('feature_demographic_mail_confirm'); }
	public function cfgAdultAge() { return $this->getConfigValue('adult_age'); }
	public function cfgChangeTime() { return $this->getConfigValue('account_changetime'); }
	public function cfgAllowGuests() { return $this->getConfigValue('allow_guest_settings') && Module_Core::instance()->cfgAllowGuests(); }
	public function cfgAllowRealName() { return $this->getConfigValue('allow_real_name'); }
	
	public function cfgAllowCountryChange() { return $this->getConfigValue('allow_country_change'); }
	public function cfgAllowLanguageChange() { return $this->getConfigValue('allow_lang_change'); }
	public function cfgAllowBirthdayChange() { return $this->getConfigValue('allow_birthday_change'); }
	public function cfgAllowGenderChange() { return $this->getConfigValue('allow_gender_change'); }
	public function cfgAllowEmailChange() { return module_enabled('Mail') && $this->getConfigValue('allow_email_change'); }
	
	public function cfgFeatureAccess() { return $this->getConfigValue('feature_access_history'); }
	public function cfgFeatureDeletion() { return $this->getConfigValue('feature_account_deletion'); }
	
	public function cfgHookRightBar() { return $this->getConfigValue('hook_right_bar'); }
	
	##############
	### Navbar ###
	##############
	public function onInitSidebar()
	{
	    if ($this->cfgHookRightBar())
	    {
	        $user = GDO_User::current();
	        if ( ($user->isMember()) ||
	            ($user->isGuest() && $this->cfgAllowGuests()) )
	        {
	            GDT_Page::$INSTANCE->rightNav->addField(
	                GDT_Link::make('btn_account')->href(href('Account', 'Settings')));
	        }
	        
	    }
	}
	
	public function renderAdminTabs()
	{
	    if (Application::instance()->isHTML())
	    {
	        GDT_Page::$INSTANCE->topTabs->addField(GDT_Template::templatePHP('Account', 'admin_tabs.php'));
	    }
	}

	public function renderAccountTabs()
	{
	    if (Application::instance()->isHTML())
	    {
	        GDT_Page::$INSTANCE->topTabs->addField(GDT_Template::templatePHP('Account', 'overview.php'));
	    }
	}
	
}
