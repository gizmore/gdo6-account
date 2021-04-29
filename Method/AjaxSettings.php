<?php
namespace GDO\Account\Method;

use GDO\Core\ModuleLoader;
use GDO\Core\GDT_JSONResponse;
use GDO\Core\GDT_Response;
use GDO\Core\MethodAjax;
use GDO\User\GDO_User;

/**
 * API Request to get all module configs.
 * Useful for JS Apps.
 * @author gizmore
 */
final class AjaxSettings extends MethodAjax
{
	public function execute()
	{
	    $user = GDO_User::current();
		$json = [];
		$modules = ModuleLoader::instance()->getEnabledModules();
		foreach ($modules as $module)
		{
			$modulename = $module->getName();
			
			foreach ($module->getSettingsCache() as $gdt)
			{
			    $gdt = $module->userSetting($user, $gdt->name);
			    
			    if ($gdt->isSerializable())
			    {
			        $json[$modulename][$gdt->name] = $gdt->configJSON();
			    }
			}
		}
		
		return GDT_Response::make()->addField(GDT_JSONResponse::make('data')->json($json));
	}

}
