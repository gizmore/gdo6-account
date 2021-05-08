<?php
namespace GDO\Account\Method;

use GDO\Account\GDO_AccountAccess;
use GDO\Account\GDO_AccountSetting;
use GDO\Account\Module_Account;
use GDO\Form\GDT_AntiCSRF;
use GDO\Form\GDT_Form;
use GDO\Form\GDT_Submit;
use GDO\Form\MethodForm;
use GDO\User\GDO_User;

/**
 * Toggle account security switches.
 * @author gizmore
 * @version 5.0
 * @since 4.0
 */
final class Security extends MethodForm
{
	public function getUserType() { return GDO_User::MEMBER; }
	public function isEnabled() { return Module_Account::instance()->cfgFeatureAccess(); }
	
	/**
	 * @var GDO_User
	 */
	private $user;
	
	/**
	 * @var GDO_AccountSetting
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
		$this->user = GDO_User::current();
		$this->settings = GDO_AccountSetting::forUser($this->user);
		Module_Account::instance()->renderAccountTabs();
		return parent::execute();
	}

	/**
	 * Take the checkboxes from GDO_AccountSetting class, which is a GDO. The columns are GDT.
	 * Add a submit button and csrf. 
	 * {@inheritDoc}
	 * @see MethodForm::createForm()
	 */
	public function createForm(GDT_Form $form)
	{
	    $form->info(t('box_account_security'));
		$form->addFields($this->settings->getGDOColumns(['accset_record_ip', 'accset_uawatch', 'accset_ipwatch', 'accset_ispwatch']));
		$form->actions()->addFields(array(
			GDT_Submit::make(),
		));
		$form->addField(GDT_AntiCSRF::make());
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
		    GDO_AccountAccess::sendAlertMail($this->getModule(), $this->user, 'record_disabled');
		}
		return parent::formValidated($form)->addField($this->renderPage());
	}
}
