<?php
namespace GDO\Account\Method;

use GDO\Account\Module_Account;
use GDO\Date\Time;
use GDO\Form\GDT_AntiCSRF;
use GDO\Form\GDT_Form;
use GDO\Form\GDT_Submit;
use GDO\Form\MethodForm;
use GDO\Template\GDT_Box;
use GDO\Template\Message;
use GDO\UI\GDT_Divider;
use GDO\User\User;
/**
 * Change account settings.
 * @author gizmore
 * @version 5.0
 * @since 2.0
 */
final class Form extends MethodForm
{
	public function isUserRequired() { return true; }
	public function isGuestAllowed() { return Module_Account::instance()->cfgAllowGuests(); }
	
	public function execute()
	{
		$delay = Time::humanDuration(Module_Account::instance()->cfgChangeTime());
		return Module_Account::instance()->renderAccountTabs()->add(
				GDT_Box::make()->html(t('infobox_account_form', [$delay]))->render()->add(
						parent::execute()));
	}
	
	################
	### The Form ###
	################
	public function createForm(GDT_Form $form)
	{
		$m = Module_Account::instance();
		$user = User::current();
		
		# Section1
		$form->addField(GDT_Divider::make('div1')->label('section_login'));
		if ($user->isGuest()) :
		$form->addField($user->gdoColumn('user_guest_name')->writable(false));
		else :
		$form->addField($user->gdoColumn('user_name')->writable(false));
		$form->addField($user->gdoColumn('user_real_name')->writable(!$user->getRealName()));
		endif;
		
		# Section2
		$form->addField(GDT_Divider::make('div2')->label('section_email'));
		$form->addField($user->gdoColumn('user_email')->writable($m->cfgAllowEmailChange()));
		$form->addField($user->gdoColumn('user_email_fmt')->writable($m->cfgAllowEmailFormatChange()));
		
		$form->addField(GDT_Divider::make('div3')->label('section_demographic'));
		$form->addField($user->gdoColumn('user_language')->writable($m->cfgAllowLanguageChange()));
		$form->addField($user->gdoColumn('user_country')->withCompletion()->writable($m->cfgAllowCountryChange()));
		if ($m->cfgAllowGenderChange()) $form->addField($user->gdoColumn('user_gender'));
		if ($m->cfgAllowBirthdayChange()) $form->addField($user->gdoColumn('user_birthdate'));

		$form->addField(GDT_Submit::make());
		$form->addField(GDT_AntiCSRF::make());
		
		$form->withGDOValuesFrom($user);
	}

	#######################
	### Change Settings ###
	#######################
	public function formValidated(GDT_Form $form)
	{
		$back = '';

		$m = Module_Account::instance();
		$user = User::current();
		$guest = $user->isGuest();
		
		# Real Name
		if ( (!$guest) && ($m->cfgAllowRealName()) )
		{
			if ($realname = $form->getFormVar('user_real_name'))
			{
			    if ($realname !== $user->getRealName())
			    {
    				$user->setVar('user_real_name', $realname);
    				$back .= t('msg_real_name_now', [$realname]);
			    }
			}
		}
		
		# Email Format
		if ( (!$guest) && $m->cfgAllowEmailFormatChange() )
		{
			$oldfmt = $user->getVar('user_email_fmt');
			$newfmt = $form->getFormVar('user_email_fmt');
			if ($newfmt !== $oldfmt)
			{
				$user->setVar('user_email_fmt', $newfmt);
				$back .= t('msg_email_fmt_now_'.$newfmt);
			}
		}
		
		# Change EMAIL
		if ( (!$guest) && ($m->cfgAllowEmailChange()) )
		{
			$oldmail = $user->getVar('user_email');
			$newmail = $form->getFormVar('user_email');
			if ($newmail !== $oldmail)
			{
				$back .= ChangeEmail::changeEmail($this->module(), $user, $newmail);
			}
		}
		
		
		# Change Demo
		$demo_changed = false;

		$oldcid = $user->getVar('user_country');
		$newcid = $m->cfgAllowCountryChange() ? $form->getFormVar('user_country') : $oldcid;
		if ($oldcid !== $newcid) { $demo_changed = true; }
		$oldlid = $user->getVar('user_language');
		$newlid = $m->cfgAllowLanguageChange() ? $form->getFormVar('user_language') : $oldlid;
		if ($oldlid !== $newlid) { $demo_changed = true; }
		$oldgender = $user->getVar('user_gender');
		$newgender = $m->cfgAllowGenderChange() ? $form->getFormVar('user_gender') : $oldgender;
		if ($oldgender !== $newgender) { $demo_changed = true; }
		$oldbirthdate = $user->getVar('user_birthdate');
		$newbirthdate = $m->cfgAllowBirthdayChange() ? $form->getFormVar('user_birthdate') : $oldbirthdate;
		if ($oldbirthdate != $newbirthdate) { $demo_changed = true; }
		
		if ($demo_changed)
		{
			if ($guest)
			{
				$user->setVars(array(
					'user_country' => $newcid,
					'user_language' => $newlid,
					'user_gender' => $newgender,
					'user_birthdate' => $newbirthdate,
				));
				$back .= t('msg_demo_changed');
			}
			else
			{
				$data = array(
					'user_country' => $newcid,
					'user_language' => $newlid,
					'user_gender' => $newgender,
					'user_birthdate' => $newbirthdate,
				);
				require_once 'ChangeDemo.php';
				$back .= ChangeDemo::requestChange($this->module(), $user, $data);
			}
		}
		
		if ($back)
		{
			$user->save();
			return Message::make($back)->add($this->renderPage());
		}
		
		return $this->renderPage();
	}
	
// 	private function changeFlag(GDT_Form $form, User $user, $flagname)
// 	{
// 		$newFlag = $form->getFormVar($flagname);
// 		if ($newFlag !== $user->getVar($flagname))
// 		{
// 			$user->setVar($flagname, $newFlag);
// 			return t('msg_'.$flagname.($newFlag?'_on':'_off'));
// 		}
// 	}
	
}
