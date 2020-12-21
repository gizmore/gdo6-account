<?php
namespace GDO\Account;

use GDO\Core\Application;
use GDO\Core\GDO_Module;
use GDO\Date\GDT_Duration;
use GDO\UI\GDT_Bar;
use GDO\DB\GDT_Checkbox;
use GDO\DB\GDT_Int;
use GDO\User\GDO_User;
use GDO\Core\GDT_Template;
use GDO\UI\GDT_Page;
use GDO\UI\GDT_Link;
/**
 * Member Account Changes.
 * 
 * @author gizmore
 * @version 6.10
 * @since 1.0
 * 
 * @see GDO_User
 */
final class Module_Account extends GDO_Module
{
	##############
	### Module ###
	##############
	public function onLoadLanguage() { return $this->loadLanguage('lang/account'); }
	public function getClasses() { return ['GDO\Account\GDO_AccountAccess', 'GDO\Account\GDO_AccountChange', 'GDO\Account\GDO_AccountDelete', 'GDO\Account\GDO_AccountSetting']; }

	##############
	### Config ###
	##############
	public function getConfig()
	{
		return array(
			GDT_Int::make('adult_age')->unsigned()->min(12)->max(40)->initial('21'),
			GDT_Duration::make('account_changetime')->min(0)->initial('90d'),
			GDT_Checkbox::make('allow_real_name')->initial('1'),
			GDT_Checkbox::make('allow_guest_settings')->initial('1'),
			GDT_Checkbox::make('allow_country_change')->initial('1'),
			GDT_Checkbox::make('allow_lang_change')->initial('1'),
			GDT_Checkbox::make('allow_birthday_change')->initial('1'),
			GDT_Checkbox::make('allow_gender_change')->initial('1'),
			GDT_Checkbox::make('allow_email_change')->initial('1'),
			GDT_Checkbox::make('allow_email_fmt_change')->initial('1'),
			GDT_Checkbox::make('feature_access_history')->initial('1'),
			GDT_Checkbox::make('feature_account_deletion')->initial('1'),
			GDT_Checkbox::make('feature_demographic_mail_confirm')->initial('1'),
		    GDT_Checkbox::make('hook_right_bar')->initial('1'),
		);
	}
	
	public function getUserSettings()
	{
		return array(
		    GDT_Checkbox::make('user_allow_email')->label('cfg_user_allow_email')->initial('0'),
		);
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
	public function cfgAllowGuests() { return $this->getConfigValue('allow_guest_settings'); }
	public function cfgAllowRealName() { return $this->getConfigValue('allow_real_name'); }
	
	public function cfgAllowCountryChange() { return $this->getConfigValue('allow_country_change'); }
	public function cfgAllowLanguageChange() { return $this->getConfigValue('allow_lang_change'); }
	public function cfgAllowBirthdayChange() { return $this->getConfigValue('allow_birthday_change'); }
	public function cfgAllowGenderChange() { return $this->getConfigValue('allow_gender_change'); }
	public function cfgAllowEmailChange() { return $this->getConfigValue('allow_email_change'); }
	public function cfgAllowEmailFormatChange() { return $this->getConfigValue('allow_email_fmt_change'); }
	
	public function cfgFeatureAccess() { return $this->getConfigValue('feature_access_history'); }
	public function cfgFeatureDeletion() { return $this->getConfigValue('feature_account_deletion'); }
	
	public function cfgHookRightBar() { return $this->getConfigValue('hook_right_bar'); }
	
	##############
	### Navbar ###
	##############
	public function onInitSidebar()
	{
// 	    if ($this->cfgHookRightBar())
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
		GDT_Page::$INSTANCE->topTabs->addField(GDT_Template::responsePHP('Account', 'admin_tabs.php'));
	}

	public function renderAccountTabs()
	{
	    GDT_Page::$INSTANCE->topTabs->addField(GDT_Template::responsePHP('Account', 'overview.php'));
	}
	
}
