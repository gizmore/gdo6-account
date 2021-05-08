<?php
namespace GDO\Account\Method;

use GDO\Account\GDO_AccountDelete;
use GDO\Account\Module_Account;
use GDO\Core\GDT_Hook;
use GDO\Form\GDT_AntiCSRF;
use GDO\Form\GDT_Form;
use GDO\Form\MethodForm;
use GDO\Mail\Mail;
use GDO\UI\GDT_Message;
use GDO\User\GDO_User;
use GDO\Date\Time;
use GDO\Form\GDT_DeleteButton;

/**
 * Delete your account.
 * @author gizmore
 * @version 6.10.1
 * @since 3.0.0
 */
final class Delete extends MethodForm
{
	public function getUserType() { return GDO_User::MEMBER; }
	public function isEnabled() { return Module_Account::instance()->cfgFeatureDeletion(); }
	
	private $prune = false;
	
	public function execute()
	{
	    if (isset($_REQUEST[$this->formName()]['prune']))
		{
			$this->prune = true; # remember to prune
			unset($_REQUEST[$this->formName()]['prune']); # Mimic normal POST
			$_REQUEST[$this->formName()]['submit'] = true; # Mimic normal POST
		}
		
		Module_Account::instance()->renderAccountTabs();
		return parent::execute();
	}
	
	public function createForm(GDT_Form $form)
	{
	    $form->info(t('box_info_deletion', [sitename()]));
		$form->addFields([
			GDT_Message::make('accrm_note'),
			GDT_AntiCSRF::make(),
		]);
		$form->actions()->addFields([
		    GDT_DeleteButton::make()->label('btn_delete_account')->confirmText('confirm_account_delete'),
		    GDT_DeleteButton::make('prune')->label('btn_prune_account')->confirmText('confirm_account_prune'),
		]);
	}
	
	public function formValidated(GDT_Form $form)
	{
		$user = GDO_User::current();
		
		# Store note in database
		if ($note = $form->getVar('accrm_note'))
		{
			GDO_AccountDelete::insertNote($user, $note);
		}
		
		# Send note as email
		$this->onSendEmail($user, $note);			
		
		if ($this->prune) # kill
		{
			$user->delete();
			# Report and logout
			return $this->message('msg_account_pruned')->addField(method('Login', 'Logout')->execute());
		}
		else # Mark deleted
		{
    		$user->saveVar('user_deleted_at', Time::getDate());
			# Report and logout
			return $this->message('msg_account_marked_deleted')->addField(method('Login', 'Logout')->execute());
		}

		GDT_Hook::callWithIPC('UserQuit', $user);
	}
	
	private function onSendEmail(GDO_User $user, $note)
	{
		foreach (GDO_User::admins() as $admin)
		{
			$sitename = sitename();
			$adminame = $admin->displayName();
			$username = $user->displayNameLabel();
			$operation = $this->prune ? tusr($admin, 'btn_prune_account') : tusr($admin, 'btn_delete_account');
			$note = htmlspecialchars($note);
			$args = [$adminame, $username, $operation, $note, $sitename];
			
			$mail = new Mail();
			$mail->setSender(GDO_BOT_EMAIL);
			$mail->setSenderName(GDO_BOT_NAME);
			$mail->setSubject(tusr($admin, 'mail_subj_account_deleted', [$sitename, $username]));
			$mail->setBody(tusr($admin, 'mail_body_account_deleted', $args));
			$mail->sendToUser($admin);
		}
	}
	
}
