<?php
namespace GDO\Account\Method;

use GDO\Account\GDO_AccountChange;
use GDO\Account\Module_Account;
use GDO\Core\Method;
use GDO\Form\GDT_AntiCSRF;
use GDO\Form\GDT_Form;
use GDO\Form\GDT_Submit;
use GDO\Mail\GDT_Email;
use GDO\Mail\Mail;
use GDO\Core\GDT;
use GDO\UI\GDT_Link;
use GDO\User\GDO_User;
use GDO\Util\Common;
use GDO\Form\GDT_Validator;
use GDO\Core\GDT_Response;

/**
 * Method only is triggered by Form (Step 0).
 * Consists of two mail sending, old and new.
 * 
 * @author gizmore
 * @version 6.10.1
 * @since 3.0.4
 */
final class ChangeEmail extends Method
{
    public function isCLI() { return false; }
	public function isUserRequired() { return false; }
	public function isEnabled() { return Module_Account::instance()->cfgAllowEmailChange(); }
	
	public function getTitleLangKey() { return 'ft_change_mail'; }
	
	public function execute()
	{
		if (isset($_REQUEST[GDT_Form::DEFAULT_NAME]['btn_changemail']))
		{
			# Step 2 - Form for mail 2
			return $this->onRequestB();
		}
		elseif ($token = Common::getRequestString('token'))
		{
			# Step 1 - from first mail
			return $this->onChangeA($token);
		}
		elseif ($token = Common::getGet('change'))
		{
			# Step 3 - from second mail
			return $this->onChangeB($token);
		}
	}
	
	#######################
	### Entry from Form ###
	#######################
	public static function changeEmail(Module_Account $module, GDO_User $user, $newMail)
	{
		if ($module->cfgDemoMail() && $user->getMail())
		{
			return self::sendEmail($module, $user, $newMail);
		}
		else
		{
			return self::sendEmailB($module, $user->getID(), $newMail);
		}
	}

	##############
	### Step 0 ###
	##############
	private static function sendEmail(Module_Account $module, GDO_User $user, $newMail)
	{
		$sitename = sitename();
		
		$mail = new Mail();
		$mail->setReceiver($user->getMail());
		$mail->setSender(GDO_BOT_EMAIL);
		$mail->setSenderName(GDO_BOT_NAME);
		$mail->setSubject(t('mail_subj_chmail_a', [$sitename]));
		$newmail = trim(htmlspecialchars($newMail));
		$link = self::createLink($module, $user, $newMail);
		$mail->setBody(t('mail_body_chmail_a', [$user->displayName(), $sitename, $newmail, $link]));
		$mail->sendToUser($user);
		return $module->message('msg_mail_sent');
	}
	
	private static function createLink(Module_Account $module, GDO_User $user, $newMail)
	{
		$userid = $user->getID();
		$row = GDO_AccountChange::addRow($userid, 'email', $newMail);
		$token = $row->getToken();
		return GDT_Link::anchor(url('Account', 'ChangeEmail', "&userid=$userid&token=$token"));
	}
	
	##############
	### Step 1 ###
	##############
	private function onChangeA($token)
	{
		if (!($row = GDO_AccountChange::getRow(Common::getRequestString('userid'), 'email', $token)))
		{
			return $this->error('err_token');
		}
		return $this->templateChangeMailB($row);
	}
	
	private function getChangeMailForm(GDO_AccountChange $ac)
	{
		$form = GDT_Form::make();
		$form->title('ft_change_mail', [sitename()]);
		$form->addFields(array(
			GDT_Email::make('email')->required(),
			GDT_Validator::make()->validator('email', [$this, 'validateEmailUnique']),
			GDT_Email::make('email_re')->required()->label('retype'),
			GDT_Validator::make()->validator('email_re', [$this, 'validateEmailRetype']),
			GDT_AntiCSRF::make(),
		));
		$form->actions()->addField(GDT_Submit::make('btn_changemail'));
		return $form;
	}
	
	public function validateEmailRetype(GDT_Form $form, GDT $gdt)
	{
		$new1 = $form->getField('email')->getVar();
		$new2 = $form->getField('email_re')->getVar();
		return $new1 === $new2 ? true : $gdt->error('err_email_retype');
	}

	public function validateEmailUnique(GDT_Form $form, GDT $gdt)
	{
		$count = GDO_User::table()->countWhere("user_email=" . quote($gdt->getVar()));
		return $count > 0 ? $gdt->error('err_email_taken') : true;
	}
	
	private function templateChangeMailB(GDO_AccountChange $ac)
	{
		$form = $this->getChangeMailForm($ac);
		return GDT_Response::makeWith($form);
	}
	
	##############
	### Step 2 ###
	##############
	private function onRequestB()
	{
		$token = Common::getRequestString('token');
		$userid = Common::getRequestString('userid');
		if (!($row = GDO_AccountChange::getRow($userid, 'email', $token)))
		{
			return $this->error('err_token');
		}
		$form = $this->getChangeMailForm($row);
		if (!$form->validateForm())
		{
			return $this->error('err_form_invalid')->addField($form);
		}
		$row->delete();
		return self::sendEmailB($this->getModule(), $userid, trim($form->getFormVar('email')));
	}
	
	private static function sendEmailB(Module_Account $module, $userid, $email)
	{
		$user = GDO_User::table()->find($userid);
		$token = GDO_AccountChange::addRow($userid, 'email2', $email);

		# Args
		$username = $user->displayName();
		$sitename = sitename();
		$email = htmlspecialchars($email);
		$link = GDT_Link::anchor(url('Account', 'ChangeEmail', "&userid={$user->getID()}&change={$token->getToken()}"));
		
		# Mail
		$mail = new Mail();
		$mail->setSender(GDO_BOT_EMAIL);
		$mail->setSenderName(GDO_BOT_NAME);
		$mail->setReceiver($email);
		$mail->setSubject(t('mail_subj_chmail_b', [$sitename]));
		$mail->setBody(t('mail_body_chmail_b', [$username, $sitename, $email, $link]));
		$mail->sendAsHTML();
		
		return $module->message('msg_mail_sent');
	}

	##############
	### Step 3 ###
	##############
	private function onChangeB($token)
	{
		if (!($ac = GDO_AccountChange::getRow(Common::getRequestString('userid'), 'email2', $token)))
		{
			return $this->error('err_token');
		}
		if (!$user = $ac->getUser())
		{
			return $this->error('err_user');
		}
		
		$user->saveVar('user_email', $ac->getData());
		
		$ac->delete();
		
		return $this->message('msg_mail_changed', [$user->getMail()]);
	}
	
}
