<?php
namespace GDO\Account\Method;

use GDO\Account\AccountAccess;
use GDO\Account\AccountSetting;
use GDO\Account\Module_Account;
use GDO\Form\GDT_AntiCSRF;
use GDO\Form\GDT_Form;
use GDO\Form\GDT_Submit;
use GDO\Form\MethodForm;
use GDO\Template\GDT_Box;
use GDO\User\User;
/**
 * Toggle account security switches.
 * @author gizmore
 * @since 4.0
 * @version 5.0
 */
final class Security extends MethodForm
{
	public function getUserType() { return User::MEMBER; }
	public function isEnabled() { return Module_Account::instance()->cfgFeatureAccess(); }
	
	/**
	 * @var User
	 */
	private $user;
	
	/**
	 * @var AccountSetting
	 */
	private $settings;
	
	/**
	 * Load user and settings used in method.
	 * Render Tabs first. Append this methods response to it.
	 * {@inheritDoc}
	 * @see MethodForm::execute()
	 */
	public function execute()
	{
		$this->user = User::current();
		$this->settings = AccountSetting::forUser($this->user);
		return Module_Account::instance()->renderAccountTabs()->add(parent::execute());
	}

	/**
	 * Take the checkboxes from AccountSetting class, which is a GDO. The columns are GDT_Base.
	 * Add a submit button and csrf. 
	 * {@inheritDoc}
	 * @see MethodForm::createForm()
	 */
	public function createForm(GDT_Form $form)
	{
		$form->addField(GDT_Box::make('info')->html(t('box_account_security')));
		$form->addFields($this->settings->getGDOColumns(['accset_record_ip', 'accset_uawatch', 'accset_ipwatch', 'accset_ispwatch']));
		$form->addFields(array(
			GDT_Submit::make(),
			GDT_AntiCSRF::make(),
		));
		$form->withGDOValuesFrom($this->settings);
	}

	/**
	 * On successful validation, save the new toggles.
	 * In case we turned IP recording off, send an error mail.
	 * {@inheritDoc}
	 * @see MethodForm::formValidated()
	 */
	public function formValidated(GDT_Form $form)
	{
		$beforeEnabeld = $this->settings->recordIPs();
		$this->settings->setVars($form->getFormData())->replace();
		if ( ($beforeEnabeld) && (!$this->settings->recordIPs()) )
		{
			AccountAccess::sendAlertMail($this->module(), $this->user, 'record_disabled');
		}
		return parent::formValidated($form)->add($this->renderPage());
	}
}
