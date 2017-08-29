<?php
namespace GDO\Account\Method;

use GDO\Account\Module_Account;
use GDO\File\GDT_File;
use GDO\Form\GDT_AntiCSRF;
use GDO\Form\GDT_Form;
use GDO\Form\GDT_Submit;
use GDO\Form\MethodForm;
use GDO\Mail\Mail;
use GDO\Template\GDT_Bar;
use GDO\Template\GDT_Box;
use GDO\UI\GDT_Link;
use GDO\User\PublicKey;
use GDO\User\User;
/**
 * Setup GPG Mail Encryption.
 * Sends a test mail before key is saved.
 * 
 * @author gizmore
 *
 */
final class Encryption extends MethodForm
{
	/**
	 * @var PublicKey
	 */
	private $key;
	
	public function isUserRequired() { return true; }
	public function isGuestAllowed() { return Module_Account::instance()->cfgAllowGuests(); }
	public function isEnabled() { return Module_Account::instance()->cfgFeatureGPGEngine(); }
	
	public function execute()
	{
		$this->key = PublicKey::getKeyForUser(User::current());
		if (isset($_POST['btn_delete']))
		{
			return Module_Account::instance()->renderAccountTabs()->add($this->onDelete()->add(parent::execute()));
		}
		return Module_Account::instance()->renderAccountTabs()->add(parent::execute());
	}

	public function createForm(GDT_Form $form)
	{
		$form->addField(GDT_Box::make('info')->html(t('infob_gpg_upload')));
		$form->addField(PublicKey::table()->gdoColumn('gpg_pubkey'));
		$form->addField(GDT_File::make('gpg_file')->action($this->href()));
		$form->addField(GDT_AntiCSRF::make());
		$btns = GDT_Bar::make();
		if ($this->key === null)
		{
			$btns->addField(GDT_Submit::make());
		}
		else
		{
			$btns->addField(GDT_Submit::make('btn_delete'));
		}
		$form->addField($btns);
		$form->withGDOValuesFrom($this->key);
	}

	##############
	### Delete ###
	##############
	public function onDelete()
	{
		if ($this->getForm()->validate())
		{
			$this->key->delete();
			return $this->message('msg_gpg_key_removed');
		}
		return $this->error('err_form_invalid');
	}

	###########
	### Add ###
	###########
	public function formValidated(GDT_Form $form)
	{
		$user = User::current();
		$outfile = GWF_PATH . 'temp/gpg/' . $user->getID();
		
		# Get file or paste
		$file_content = '';
		$files = $form->getFormVar('gpg_file');
		if (count($files)>0)
		{
			$file = $files[0];
			$file_content = file_get_contents($file['path']);
		}
		else { $file_content = $form->getFormVar('gpg_pubkey'); }
		$file_content = trim($file_content);
		
		
		if (strpos($file_content, '-----BEGIN ') !== 0)
		{
			$response = $this->error('err_gpg_not_start_with_begin');
		}
		elseif (!file_put_contents($outfile, $file_content, GWF_CHMOD))
		{
			$response = $this->error('err_write_file');
		}
		elseif (!($fingerprint = PublicKey::grabFingerprint($file_content)))
		{
			$response = $this->error('err_gpg_fail_fingerprinting');
		}
		else
		{
			$response = $this->sendGPGMail($user, $fingerprint);
		}
		
		return $response->add($this->renderPage());
	}
	
	private function sendGPGMail(User $user, $fingerprint)
	{
		if (!($email = $user->getMail()))
		{
			return $this->error('err_user_has_no_email');
		}
		$mail = new Mail();
		$mail->setSender(GWF_BOT_EMAIL);
		$mail->setSenderName(GWF_BOT_NAME);
		$mail->setReceiver($email);
		$mail->setGPGKey($fingerprint);
		$mail->setSubject(t('mail_subj_gpg', [sitename()]));
		$mail->setBody($this->getGPGMailBody($user, $fingerprint));
		$mail->sendToUser($user);
		return $this->message('msg_gpg_mail_sent');
	}
	
	private function getGPGMailBody(User $user, $fingerprint)
	{
		$link = GDT_Link::anchor(url('Account', 'SetGPGKey', "&userid={$user->getID()}&token={$fingerprint}"));
		$args = [$user->displayName(), sitename(), $link];
		return tusr($user, 'mail_body_gpg', $args);
	}
	
}
