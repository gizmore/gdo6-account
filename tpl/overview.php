<?php
use GDO\Account\Module_Account;
use GDO\UI\GDT_Bar;
use GDO\UI\GDT_Link;

$module = Module_Account::instance();

# Create a horizontal navbar.
$bar = GDT_Bar::make();

# Add buttons to bar
$bar->addField(GDT_Link::make('link_account_form')->href(href('Account', 'Form'))->icon('account_box'));
$bar->addField(GDT_Link::make('link_settings')->href(href('Account', 'Settings'))->icon('settings'));
if ($module->cfgFeatureGPGEngine()) :
	$bar->addField(GDT_Link::make('link_account_encryption')->href(href('Account', 'Encryption'))->icon('enhanced_encryption'));
endif;
if ($module->cfgFeatureAccess()) : 
	$bar->addField(GDT_Link::make('link_account_security')->href(href('Account', 'Security'))->icon('alarm_on'));
	$bar->addField(GDT_Link::make('link_account_access')->href(href('Account', 'Access'))->icon('date_range'));
endif;
if ($module->cfgFeatureDeletion()) :
	$bar->addField(GDT_Link::make('link_account_delete')->href(href('Account', 'Delete'))->icon('delete_sweep'));
endif;

# Render
echo $bar->renderCell();
