<?php
namespace GDO\Account\Method;

use GDO\Account\Module_Account;
use GDO\Core\GDO_Module;
use GDO\Form\GDT_AntiCSRF;
use GDO\Form\GDT_Form;
use GDO\Form\GDT_Submit;
use GDO\Form\MethodForm;
use GDO\UI\GDT_Bar;
use GDO\UI\GDT_Panel;
use GDO\UI\GDT_Divider;
use GDO\UI\GDT_Link;
use GDO\User\GDO_UserSetting;
use GDO\Util\Common;
use GDO\Core\ModuleLoader;
use GDO\User\GDO_UserSettingBlob;
use GDO\Core\GDT;
use GDO\Core\GDT_Response;
/**
 * Generic setting functionality.
 * Simply return GDT[] in Module->getUserSettings() and you can configure stuff.
 * 
 * @author gizmore
 * @since 5.0
 */
final class Settings extends MethodForm
{
	public function isUserRequired() { return true; }
	
	/**
	 * @var GDO_Module
	 */
	private $configModule;
	
	public function execute()
	{
		$tabs = Module_Account::instance()->renderAccountTabs();
		if ($this->configModule = ModuleLoader::instance()->getModule(Common::getGetString('module')))
		{
			return $tabs->add($this->navModules())->add(parent::execute());
		}
		return $tabs->add($this->navModules())->add($this->infoBox());
	}
	
	public function infoBox()
	{
		return GDT_Response::makeWith(GDT_Panel::make()->html(t('box_content_account_settings')));
	}
	
	public function navModules()
	{
		$navbar = GDT_Bar::make()->horizontal();
		foreach (ModuleLoader::instance()->getEnabledModules() as $module)
		{
			if ($module->getUserSettings() || $module->getUserSettingBlobs() || $module->getUserConfig())
			{
				$name = $module->getName();
				$href = href('Account', 'Settings', "&module=$name");
				$button = GDT_Link::make("link_$name")->rawlabel($name)->href($href)->icon('settings');
				$navbar->addField($button);
			}
		}
		return GDT_Response::makeWith($navbar);
	}
	
	public function createForm(GDT_Form $form)
	{
		$moduleName = $this->configModule->getName();
		$this->title(t('ft_account_settings', [$moduleName]));
		if ($settings = $this->configModule->getUserSettings())
		{
			$form->addField(GDT_Divider::make()->label('div_user_settings', [$moduleName]));
			foreach ($settings as $gdoType)
			{
				$gdt = GDO_UserSetting::get($gdoType->name);
				$gdt->label('cfg_'.$gdoType->name);
				$form->addField($gdt);
			}
		}
		if ($settings = $this->configModule->getUserSettingBlobs())
		{
			$form->addField(GDT_Divider::make()->label('div_user_textual_settings', [$moduleName]));
			foreach ($settings as $gdoType)
			{
				$gdt = GDO_UserSettingBlob::get($gdoType->name);
				$gdt->label('cfg_'.$gdoType->name);
				$form->addField($gdt);
			}
		}
		if ($settings = $this->configModule->getUserConfig())
		{
			$form->addField(GDT_Divider::make()->label('div_variables', [$moduleName]));
			foreach ($settings as $gdoType)
			{
				$gdt = GDO_UserSetting::get($gdoType->name)->editable(false);
				$gdt->label('cfg_'.$gdoType->name);
				$form->addField($gdt);
			}
		}
		$form->addField(GDT_AntiCSRF::make());
		$form->addField(GDT_Submit::make());
	}
	
	public function formValidated(GDT_Form $form)
	{
		$info = [];
		$error = [];
		foreach ($form->fields as $gdoType)
		{
			if ( ($gdoType->writable) && ($gdoType->editable) )
			{
				$key = $gdoType->name;
				$old = $gdoType->initial;
				$new = $gdoType->getVar($key);
				if ($old !== $new)
				{
					if (!$gdoType->validate($gdoType->toValue($new)))
					{
						$error[] = t('err_settings_save', $gdoType->error);
						continue;
					}
					if ($this->isSettingBlob($gdoType))
					{
						GDO_UserSettingBlob::set($key, $new);
					}
					else
					{
						GDO_UserSetting::set($key, $new);
					}
					$old = $old === null ? '<i class="null">null</i>' : html($old);
					$new = $new === null ? '<i class="null">null</i>' : html($new);
					$info[] = t('msg_modulevar_changed', [$gdoType->displayLabel(), $old, $new]);
				}
			}
		}
		
		$page = $this->renderPage();
		
		if (!empty($error))
		{
			return $this->error('err_settings_saved', [$this->configModule->getName(), implode('<br/>', $info)])->add($page);
		}
		
		if (!empty($info))
		{
		  return $this->message('msg_settings_saved', [$this->configModule->getName(), implode('<br/>', $info)])->add($page);
		}
		
		return $page;
	}
	
	private function isSettingBlob(GDT $gdoType)
	{
		return GDO_UserSettingBlob::isRegistered($gdoType->name);
	}
		
}
