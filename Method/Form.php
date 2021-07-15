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
 * @version 6.10.4
 * @since 1.2.0
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
		
		if ($delaytime = Module_Account::instance()->cfgChangeTime())
		{
    		$delay = Time::humanDuration($delaytime);
    		$form->info(t('infobox_account_form', [$delay]));
		}
		
		# Section1
		$form->addField(GDT_Divider::make('div1')->label('section_login'));
		if ($user->isGuest()) :
		$form->addField($user->gdoColumn('user_guest_name')->writable(false));
		else :
		$form->addField($user->gdoColumn('user_name')->writable(false));
		$form->addField($user->gdoColumn('user_real_name')->writable(!$user->getRealName()));
		endif;
		
		if (module_enabled('Mail'))
		{
		    $form->addField($user->gdoColumn('user_email')->writable($m->cfgAllowEmailChange()));
		}
		
		# section 2
		$form->addField(GDT_Divider::make('div4')->label('timezone'));
		$form->addField($user->gdoColumn('user_timezone'));
		
		# section 3
		$form->addField(GDT_Divider::make('div3')->label('section_demographic'));
		$form->addField($user->gdoColumn('user_language')->writable($m->cfgAllowLanguageChange()));
		$form->addField($user->gdoColumn('user_country')->withCompletion()->writable($m->cfgAllowCountryChange())->emptyInitial(t('no_country')));
		if ($m->cfgAllowGenderChange()) $form->addField($user->gdoColumn('user_gender'));

		$form->actions()->addField(GDT_Submit::make());
		$form->addField(GDT_AntiCSRF::make());
		
// 		$form->withGDOValuesFrom($user);
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
		
		$old = $user->getTimezone();
		$new = $form->getFormVar('user_timezone');
		if ($old !== $new)
		{
    		$user->tempUnset('timezone');
    		$user->setVar('user_timezone', $new);
		}
		
		# Real Name
		if ( (!$guest) && ($m->cfgAllowRealName()) )
		{
			if ($realname = $form->getFormVar('user_real_name'))
			{
				if ( ($realname !== $user->getRealName()) && (!$user->getRealName()) )
				{
					$user->setVar('user_real_name', $realname);
					$back->addField($this->message('msg_real_name_now', [$realname]));
				}
			}
		}
		
		# Change EMAIL
		if ($m->cfgAllowEmailChange())
		{
			$oldmail = $user->getVar('user_email');
			$newmail = $form->getFormVar('user_email');
			if ($newmail !== $oldmail)
			{
			    $back->addField(ChangeEmail::changeEmail($this->getModule(), $user, $newmail));
			}
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
		
		if ($demo_changed)
		{
			$demo_vars = [
				'user_country' => $newcid,
				'user_language' => $newlid,
				'user_gender' => $newgender,
			];
			
			$delaytime = Module_Account::instance()->cfgChangeTime();
			
			if (($guest) || (!$delaytime))
			{
				$user->setVars($demo_vars);
				$back->addField($this->message('msg_demo_changed'));
			}
			else
			{
				$data = $demo_vars;
				$back->addField(ChangeDemo::requestChange($this->getModule(), $user, $data));
			}
		}
		$user->save();
		return $back->addField($this->renderPage());
	}
	
}
