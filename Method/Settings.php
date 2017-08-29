<?php
namespace GDO\Account\Method;

use GDO\Account\Module_Account;
use GDO\Core\Application;
use GDO\Core\Module;
use GDO\Form\GDT_AntiCSRF;
use GDO\Form\GDT_Form;
use GDO\Form\GDT_Submit;
use GDO\Form\MethodForm;
use GDO\Template\GDT_Bar;
use GDO\Template\GDT_Box;
use GDO\UI\GDT_Divider;
use GDO\UI\GDT_Link;
use GDO\User\UserSetting;
use GDO\Util\Common;
use GDO\Core\ModuleLoader;
use GDO\Forum\GDT_ForumSubscribe;
use GDO\Template\Response;
/**
 * Generic setting functionality.
 * Simply return GDT_Base[] in Module->getUserSettings() and you can configure stuff.
 * 
 * @author gizmore
 * @since 5.0
 */
final class Settings extends MethodForm
{
	public function isUserRequired() { return true; }
	
	/**
	 * @var Module
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
		return GDT_Box::make()->html(t('box_content_account_settings'))->render();
	}
	
	public function navModules()
	{
		$navbar = GDT_Bar::make();
		foreach (ModuleLoader::instance()->getActiveModules() as $module)
		{
			if ($module->getUserSettings() || $module->getUserConfig())
			{
				$name = $module->getName();
				$href = href('Account', 'Settings', "&module=$name");
				$button = GDT_Link::make("link_$name")->rawlabel($name)->href($href)->icon('settings');
				$navbar->addField($button);
			}
		}
		return $navbar->render();
	}
	
	public function createForm(GDT_Form $form)
	{
	    $moduleName = $this->configModule->getName();
		$this->title(t('ft_account_settings', [sitename(), $moduleName]));
		if ($settings = $this->configModule->getUserSettings())
		{
		    $form->addField(GDT_Divider::make()->label('div_user_settings', [$moduleName]));
		    foreach ($settings as $gdoType)
		    {
		        $form->addField(UserSetting::get($gdoType->name));
		    }
		}
		if ($settings = $this->configModule->getUserConfig())
		{
		    $form->addField(GDT_Divider::make()->label('div_variables', [$moduleName]));
			foreach ($settings as $gdoType)
			{
			    $form->addField(UserSetting::get($gdoType->name)->writable(false));
			}
		}
		$form->addField(GDT_AntiCSRF::make());
		$form->addField(GDT_Submit::make());
	}
	
	public function formValidated(GDT_Form $form)
	{
		$info = [];
		foreach ($form->fields as $gdoType)
		{
			if ( ($gdoType->writable) && ($gdoType->editable) )
			{
				$key = $gdoType->name;
				$old = $gdoType->initial;
				$new = $gdoType->getVar($key);
				if ($old !== $new)
				{
					UserSetting::set($key, $new);
					$old = $old === null ? '<i class="null">null</i>' : html($old);
					$new = $new === null ? '<i class="null">null</i>' : html($new);
					$info[] = t('msg_modulevar_changed', [$gdoType->label, $old, $new]);
				}
			}
		}
		
		$page = $this->renderPage();
		
		return empty($info) ? $page : 
		  $this->message('msg_settings_saved', [$this->configModule->getName(), implode('<br/>', $info)])->add($page);
	}
}
