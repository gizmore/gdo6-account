<?php
namespace GDO\Account;

use GDO\Core\Application;
use GDO\Core\Module;
use GDO\Date\GDT_Duration;
use GDO\Date\Time;
use GDO\Template\GDT_Bar;
use GDO\Type\GDT_Checkbox;
use GDO\Type\GDT_Int;
use GDO\User\User;
/**
 * Member Account Changes.
 * 
 * @author gizmore
 * @version 5.0
 * @since 1.0
 * 
 * @see User
 */
final class Module_Account extends Module
{
	##################
	### Module ###
	##################
	public function onLoadLanguage() { return $this->loadLanguage('lang/account'); }
	public function getClasses() { return ['GDO\Account\AccountAccess', 'GDO\Account\AccountChange', 'GDO\Account\AccountDelete', 'GDO\Account\AccountSetting']; }

	##############
	### Config ###
	##############
	public function getConfig()
	{
		return array(
			GDT_Int::make('adult_age')->unsigned()->min(12)->max(40)->initial('21'),
			GDT_Duration::make('account_changetime')->min(0)->initial(Time::ONE_MONTH * 3),
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
			GDT_Checkbox::make('feature_gpg_engine')->initial('1'),
			GDT_Checkbox::make('feature_demographic_mail_confirm')->initial('1'),
		);
	}
	
	#############
	### Hooks ###
	#############
	public function hookUserAuthenticated(User $user)
	{
		if (!Application::instance()->isCLI())
		{
			AccountAccess::onAccess($this, $user);
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
	public function cfgFeatureGPGEngine() { return $this->getConfigValue('feature_gpg_engine'); }
	
	##############
	### Navbar ###
	##############
	/**
	 * Add account link to right sidebar, if user can use it.
	 */
	public function hookRightBar(GDT_Bar $navbar)
	{
		$this->templatePHP('rightbar.php', ['navbar' => $navbar]);
	}
	
	public function renderAdminTabs()
	{
		return $this->templatePHP('admin_tabs.php');
	}

	public function renderAccountTabs()
	{
		return $this->templatePHP('overview.php');
	}
	
}
