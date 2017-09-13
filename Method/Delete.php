<?php
namespace GDO\Account\Method;
use GDO\Account\GDO_AccountDelete;
use GDO\Account\Module_Account;
use GDO\Core\GDT_Hook;
use GDO\Form\GDT_AntiCSRF;
use GDO\Form\GDT_Form;
use GDO\Form\GDT_Submit;
use GDO\Form\MethodForm;
use GDO\Mail\Mail;
use GDO\Template\GDT_Box;
use GDO\Type\GDT_Message;
use GDO\User\GDO_User;
/**
 * Delete your account.
 * @author gizmore
 */
final class Delete extends MethodForm
{
	public function getUserType() { return GDO_User::MEMBER; }
	public function isEnabled() { return Module_Account::instance()->cfgFeatureDeletion(); }
	
	private $prune = false;
	
	public function execute()
	{
		if (isset($_POST['prune']))
		{
			$this->prune = true; # remember to prune
			$_REQUEST['submit'] = true; # Mimic normal POST
		}
		return Module_Account::instance()->renderAccountTabs()->add(parent::execute());
	}
	
	public function createForm(GDT_Form $form)
	{
		$fields = array(
			GDT_Box::make('info')->html(t('box_info_deletion', [sitename()])),
			GDT_Message::make('accrm_note'),
			GDT_Submit::make()->label('btn_delete_account'),
			GDT_Submit::make('prune')->label('btn_prune_account'),
			GDT_AntiCSRF::make(),
		);
		$form->addFields($fields);
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
		
		# Mark deleted
		$user->saveValue('user_deleted_at', time());
		GDT_Hook::call('UserQuit', $user);
		if ($this->prune)
		{
			$user->delete();
			# Report and logout
			return $this->message('msg_account_pruned')->add(method('Login', 'Logout')->execute());
		}
		else
		{
			# Report and logout
			return $this->message('msg_account_marked_deleted')->add(method('Login', 'Logout')->execute());
		}
	}
	
	private function onSendEmail(GDO_User $user, $note)
	{
		foreach (GDO_User::admins() as $admin)
		{
			$sitename = sitename();
			$adminame = $admin->displayName();
			$username = $user->displayName();
			$operation = $this->prune ? tusr($admin, 'btn_prune_account') : tusr($admin, 'btn_delete_account');
			$note = htmlspecialchars($note);
			$args = [$adminame, $username, $operation, $note, $sitename];
			
			$mail = new Mail();
			$mail->setSender(GWF_BOT_EMAIL);
			$mail->setSenderName(GWF_BOT_NAME);
			$mail->setSubject(tusr($admin, 'mail_subj_account_deleted', [$sitename, $username]));
			$mail->setBody(tusr($admin, 'mail_body_account_deleted', $args));
			$mail->sendToUser($admin);
		}
	}
}

