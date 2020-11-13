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
use GDO\Util\Common;
use GDO\Core\ModuleLoader;
use GDO\Core\GDT;
use GDO\Core\GDT_Response;
use GDO\Core\GDT_Hook;
use GDO\User\GDO_User;
use GDO\Language\Trans;
use GDO\UI\GDT_Page;
use GDO\Core\Website;
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
	
	public function beforeExecute()
	{
	    Module_Account::instance()->renderAccountTabs();
	    GDT_Page::$INSTANCE->topTabs->addField($this->navModules());
	}
	
	public function execute()
	{
		if ($this->configModule = ModuleLoader::instance()->getModule(Common::getGetString('module')))
		{
			return parent::execute();
		}
		return $this->infoBox();
	}
	
	public function infoBox()
	{
		return GDT_Response::makeWith(GDT_Panel::make()->title(t('link_settings'))->html(t('box_content_account_settings')));
	}
	
	public function navModules()
	{
		$navbar = GDT_Bar::make()->horizontal();
		foreach (ModuleLoader::instance()->getEnabledModules() as $module)
		{
			$href = $module->getUserSettingsURL();
			if ($module->getUserSettings() || $module->getUserSettingBlobs() || $href)
			{
				$name = $module->getName();
				$href = $href ? $href : href('Account', 'Settings', "&module=$name");
				$button = GDT_Link::make("link_$name")->rawlabel($name)->href($href)->icon('settings');
				$navbar->addField($button);
			}
		}
		Website::topResponse()->addField($navbar);
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
				$gdt = $this->configModule->setting($gdoType->name);
				if (Trans::hasKey('cfg_'.$gdoType->name) || (!$gdt->hasName()))
				{
					$gdt->label('cfg_'.$gdoType->name);
				}
				$form->addField($gdt);
			}
		}
		if ($settings = $this->configModule->getUserSettingBlobs())
		{
			$form->addField(GDT_Divider::make()->label('div_user_textual_settings', [$moduleName]));
			foreach ($settings as $gdoType)
			{
			    $gdt = $this->configModule->setting($gdoType->name);
				if (Trans::hasKey('cfg_'.$gdoType->name) || (!$gdt->hasName()))
				{
					$gdt->label('cfg_'.$gdoType->name);
				}
				$form->addField($gdt);
			}
		}
		if ($settings = $this->configModule->getUserConfig())
		{
			$form->addField(GDT_Divider::make()->label('div_variables', [$moduleName]));
			foreach ($settings as $gdoType)
			{
			    $gdt = $this->configModule->setting($gdoType->name);
				if (Trans::hasKey('cfg_'.$gdoType->name) || (!$gdt->hasName()))
				{
					$gdt->label('cfg_'.$gdoType->name);
				}
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
		$changes = array();
		foreach ($form->fields as $gdoType)
		{
			if ( ($gdoType->writable) && ($gdoType->editable) )
			{
				$key = $gdoType->name;
				$old = $gdoType->initial;
				$new = $gdoType->getVar($key);
				if ($old !== $new)
				{
					$changes[$key] = array($old, $new);
					if (!$gdoType->validate($gdoType->toValue($new)))
					{
						$error[] = t('err_settings_save', $gdoType->error);
						continue;
					}
					$this->configModule->saveSetting($key, $new);
					$old = $old === null ? '<i class="null">null</i>' : html($gdoType->displayValue($old));
					$new = $new === null ? '<i class="null">null</i>' : html($gdoType->displayValue($new));
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
			GDT_Hook::callHook('UserSettingSaved', $this->configModule, GDO_User::current(), $changes);
			return $this->message('msg_settings_saved', [$this->configModule->getName(), implode('<br/>', $info)])->add($page);
		}
		
		
		return $page;
	}
	
}
