<?php
namespace GDO\Account\Method;
use GDO\Core\Method;
use GDO\Core\ModuleLoader;
use GDO\Core\GDT_JSONResponse;
use GDO\Core\GDT_Response;
use GDO\User\GDO_UserSetting;
use GDO\User\GDO_UserSettingBlob;
/**
 * API Request to get all module configs.
 * Useful for JS Apps.
 * @author gizmore
 */
final class AjaxSettings extends Method
{
    public function execute()
    {
        $json = [];
        $modules = ModuleLoader::instance()->getEnabledModules();
        foreach ($modules as $module)
        {
            $modulename = $module->getName();
            if ($settings = $module->getUserSettings())
            {
                foreach ($settings as $gdt)
                {
                    $json[$modulename][$gdt->name] = $gdt->configJSON();
                    $json[$modulename][$gdt->name]['type'] = get_class($gdt);
                    $json[$modulename][$gdt->name]['value'] = GDO_UserSetting::get($gdt->name)->initial;
                    $json[$modulename][$gdt->name]['help'] = t('cfg_'.$gdt->name);
                }
            }
            if ($settings = $module->getUserSettingBlobs())
            {
                foreach ($settings as $gdt)
                {
                    $json[$modulename][$gdt->name] = $gdt->configJSON();
                    $json[$modulename][$gdt->name]['type'] = get_class($gdt);
                    $json[$modulename][$gdt->name]['value'] = GDO_UserSettingBlob::get($gdt->name)->initial;
                    $json[$modulename][$gdt->name]['help'] = t('cfg_'.$gdt->name);
                }
            }
        }
        
        return GDT_Response::make()->addField(GDT_JSONResponse::make()->json($json));
    }
}
