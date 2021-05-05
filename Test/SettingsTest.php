<?php
namespace GDO\Account\Test;

use GDO\Tests\TestCase;
use GDO\Tests\MethodTest;
use GDO\Account\Method\Settings;

/**
 * Tests for the Settings method.
 * @author gizmore
 */
final class SettingsTest extends TestCase
{
    /**
     * Test if settings can be saved.
     */
    public function testSaveSettings()
    {
        $this->userGizmore();
        $m = Settings::make();
        $p = [
            
        ];
        $gp = [
            'module' => 'Account',
        ];
        MethodTest::make()->parameters($p)->getParameters($gp)->method($m)->execute();
        $this->assert200("Check account settings method for 200 response code");
    }
    
}
