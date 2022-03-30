<?php
namespace GDO\Account\Method;

use GDO\Account\Module_Account;
use GDO\Core\GDO_Module;
use GDO\Form\GDT_AntiCSRF;
use GDO\Form\GDT_Form;
use GDO\Form\GDT_Submit;
use GDO\Form\MethodForm;
use GDO\UI\GDT_Panel;
use GDO\UI\GDT_Divider;
use GDO\UI\GDT_Link;
use GDO\Core\Application;
use GDO\Core\ModuleLoader;
use GDO\Core\GDT_Response;
use GDO\Core\GDT_Hook;
use GDO\User\GDO_User;
use GDO\Language\Trans;

/**
 * Generic setting functionality.
 * Simply return GDT[] in Module->getUserSettings() and you can configure stuff.
 * 
 * @author gizmore
 * @version 6.10.4
 * @since 5.0.0
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
	    if (Application::instance()->isHTML())
	    {
    	    Module_Account::instance()->renderAccountTabs();
	    }
	}
	
	public function getTitle()
	{
	    $mod = $this->configModule ? 
	       $this->configModule->displayName() :
	       sitename();
	    return t('ft_account_settings', [$mod]);
	}
	
	public function execute()
	{
		$modulename = (string)@$_REQUEST['module'];
		if ($this->configModule = ModuleLoader::instance()->getModule($modulename))
		{
			return parent::execute();
		}
		return $this->infoBox();
	}
	
	public function infoBox()
	{
		return GDT_Response::makeWith(
		    GDT_Panel::make()->title('link_settings')->text('box_content_account_settings'));
	}
	
	public function navLinks()
	{
	    $links = [];
	    $modules = [];
		foreach (ModuleLoader::instance()->getEnabledModules() as $module)
		{
		    $href = $module->getUserSettingsURL();
		    if ($module->getUserSettings() || $module->getUserSettingBlobs() || $href)
		    {
		        $modules[] = $module;
		    }
		}
		
		foreach ($modules as $module)
		{
		    $href = $module->getUserSettingsURL();
			$name = $module->getName();
			$href = $href ? $href : href('Account', 'Settings', "&module=$name");
			$button = GDT_Link::make("link_$name")->labelRaw($name)->href($href)->icon('settings');
			$links[] = $button;
		}
		return $links;
	}
	
	public function createForm(GDT_Form $form)
	{
	    if (!$this->configModule)
	    {
	        return;
	    }
		$moduleName = $this->configModule->getName();
		$this->title(t('ft_account_settings', [$moduleName]));
		if ($settings = $this->configModule->getUserSettings())
		{
			$form->addField(GDT_Divider::make()->label('div_user_settings', [$moduleName]));
			foreach ($settings as $gdt)
			{
				$gdt = $this->configModule->setting($gdt->name);
				if (Trans::hasKey('cfg_'.$gdt->name) || (!$gdt->hasName()))
				{
					$gdt->label('cfg_'.$gdt->name);
				}
				$form->addField($gdt);
			}
		}
		if ($settings = $this->configModule->getUserSettingBlobs())
		{
			$form->addField(GDT_Divider::make()->label('div_user_textual_settings', [$moduleName]));
			foreach ($settings as $gdt)
			{
			    $gdt = $this->configModule->setting($gdt->name);
				if (Trans::hasKey('cfg_'.$gdt->name) || (!$gdt->hasName()))
				{
					$gdt->label('cfg_'.$gdt->name);
				}
				$form->addField($gdt);
			}
		}
		if ($settings = $this->configModule->getUserConfig())
		{
			$form->addField(GDT_Divider::make()->label('div_variables', [$moduleName]));
			foreach ($settings as $gdt)
			{
			    $gdt = $this->configModule->setting($gdt->name);
				if (Trans::hasKey('cfg_'.$gdt->name) || (!$gdt->hasName()))
				{
					$gdt->label('cfg_'.$gdt->name);
				}
				$form->addField($gdt);
			}
		}
		$form->addField(GDT_AntiCSRF::make()->fixed());
		$form->actions()->addField(GDT_Submit::make());
	}
	
	public function formValidated(GDT_Form $form)
	{
		$info = [];
		$error = [];
		$changes = array();
		foreach ($form->fields as $gdt)
		{
			if ( ($gdt->writable) && ($gdt->editable) ) # can change?
			{
				$key = $gdt->name;
				$old = $gdt->initial;
				$new = $gdt->getVar();
				
				# Changed?
				if ($old !== $new)
				{
					$changes[$key] = array($old, $new);
					
					# Validate first
					if (!$gdt->validate($gdt->getValue()))
					{
						$error[] = t('err_settings_save', $gdt->error);
						continue;
					}
					
					# Save
					$this->configModule->saveSetting($key, $new);
					
					# Prepare response text
					$old = $old === null ?
					   '<i class="null gdo-setting-old">null</i>' :
					   '<i class="gdo-setting-old">' . $gdt->displayVar($old) . '</i>';
					$new = $new === null ?
					   '<i class="null gdo-setting-new">null</i>' :
					   '<i class="gdo-setting-new">' . $gdt->displayVar($new) . '</i>';
					$name  = sprintf('<i class="gdo-setting-name">%s</i>', $gdt->displayLabel());
					$info[] = t('msg_modulevar_changed', [$name, $old, $new]);
				}
			}
		}
		
		# Reset form
		$this->resetForm();
		
		# Add form page to response later
		$page = $this->renderPage();
		
		# Quit on error
		if (!empty($error))
		{
			return $this->error('err_settings_saved',
			    [$this->configModule->getName(), implode('<br/>', $info)])->addField($page);
		}
		
		# Saved on success
		if (!empty($info))
		{
		    # Call hook on changes
			GDT_Hook::callHook('UserSettingSaved',
			    $this->configModule, GDO_User::current(), $changes);
			# Print changes
			return $this->message('msg_settings_saved',
			    [$this->configModule->getName(), implode('<br/>', $info)])->addField($page);
		}
		
		
		return $page;
	}
	
}
