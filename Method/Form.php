<?php
namespace GDO\Account\Method;

use GDO\Account\Module_Account;
use GDO\Date\Time;
use GDO\Form\GDT_AntiCSRF;
use GDO\Form\GDT_Form;
use GDO\Form\GDT_Submit;
use GDO\Form\MethodForm;
use GDO\UI\GDT_Divider;
use GDO\User\GDO_User;
use GDO\Core\GDT_Response;

/**
 * Change account settings.
 * @author gizmore
 * @version 6.10
 * @since 2.0
 */
final class Form extends MethodForm
{
	public function isUserRequired() { return true; }
	public function isGuestAllowed() { return Module_Account::instance()->cfgAllowGuests(); }
	
	public function beforeExecute()
	{
	    Module_Account::instance()->renderAccountTabs();
	}
	
	################
	### The Form ###
	################
	public function createForm(GDT_Form $form)
	{
		$m = Module_Account::instance();
		$user = GDO_User::current();
		
		$delay = Time::humanDuration(Module_Account::instance()->cfgChangeTime());
		$form->info(t('infobox_account_form', [$delay]));
		
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
		$form->addField($m->userSetting($user, 'user_allow_email')->editable(true));
		
		$form->addField(GDT_Divider::make('div4')->label('timezone'));
		$form->addField($user->gdoColumn('user_timezone'));
		
		$form->addField(GDT_Divider::make('div3')->label('section_demographic'));
		$form->addField($user->gdoColumn('user_language')->writable($m->cfgAllowLanguageChange()));
		$form->addField($user->gdoColumn('user_country')->withCompletion()->writable($m->cfgAllowCountryChange())->emptyInitial(t('no_country')));
		
		if ($m->cfgAllowGenderChange()) $form->addField($user->gdoColumn('user_gender'));
		if ($m->cfgAllowBirthdayChange()) $form->addField($user->gdoColumn('user_birthdate'));

		$form->actions()->addField(GDT_Submit::make());
		$form->addField(GDT_AntiCSRF::make());
		
		$form->withGDOValuesFrom($user);
	}

	#######################
	### Change Settings ###
	#######################
	public function formValidated(GDT_Form $form)
	{
		$back = GDT_Response::make();

		$m = Module_Account::instance();
		$user = GDO_User::current();
		$guest = $user->isGuest();
		
		$user->setVar('user_timezone', $form->getFormVar('user_timezone'));
		
		# Real Name
		if ( (!$guest) && ($m->cfgAllowRealName()) )
		{
			if ($realname = $form->getFormVar('user_real_name'))
			{
				if ( ($realname !== $user->getRealName()) && (!$user->getRealName()) )
				{
					$user->setVar('user_real_name', $realname);
					$back->add($this->message('msg_real_name_now', [$realname]));
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
				$back->add($this->message('msg_email_fmt_now_'.$newfmt));
			}
		}
		
		# Change EMAIL
		if ( (!$guest) && ($m->cfgAllowEmailChange()) )
		{
			$oldmail = $user->getVar('user_email');
			$newmail = $form->getFormVar('user_email');
			if ($newmail !== $oldmail)
			{
			    $back->add(ChangeEmail::changeEmail($this->getModule(), $user, $newmail));
			}
		}
		
		# Allow Mail
		$field = $form->getField('user_allow_email');
		$var = $form->getFormVar('user_allow_email');
		if ($field->initial !== $var)
		{
		    $m->saveUserSetting($user, 'user_allow_email', $var);
		    $back->add($this->message('msg_change_allow_email', [$field->displayValue($var)]));
		}
		
		# Change Demo
		$demo_changed = false;

		$oldcid = $user->getVar('user_country');
		$newcid = $m->cfgAllowCountryChange() ? $form->getFormVar('user_country') : $oldcid;
		if ($oldcid != $newcid) { $demo_changed = true; }
		$oldlid = $user->getVar('user_language');
		$newlid = $m->cfgAllowLanguageChange() ? $form->getFormVar('user_language') : $oldlid;
		if ($oldlid != $newlid) { $demo_changed = true; }
		$oldgender = $user->getVar('user_gender');
		$newgender = $m->cfgAllowGenderChange() ? $form->getFormVar('user_gender') : $oldgender;
		if ($oldgender != $newgender) { $demo_changed = true; }
		$oldbirthdate = $user->getVar('user_birthdate');
		$newbirthdate = $m->cfgAllowBirthdayChange() ? $form->getFormVar('user_birthdate') : $oldbirthdate;
		if ($oldbirthdate != $newbirthdate) { $demo_changed = true; }
		
		if ($demo_changed)
		{
			$demo_vars = array(
				'user_country' => $newcid,
				'user_language' => $newlid,
				'user_gender' => $newgender,
				'user_birthdate' => $newbirthdate,
			);
			
			if ($guest)
			{
				$user->setVars($demo_vars);
				$back->add($this->message('msg_demo_changed'));
			}
			else
			{
				$data = $demo_vars;
				$back->add(ChangeDemo::requestChange($this->getModule(), $user, $data));
			}
		}
		$user->save();
		return $back->add($this->renderPage());
	}
	
}
