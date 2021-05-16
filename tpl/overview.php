<?php
use GDO\Account\Module_Account;
use GDO\UI\GDT_Bar;
use GDO\UI\GDT_Link;

$module = Module_Account::instance();

# Create a horizontal navbar.
$bar = GDT_Bar::make()->horizontal();

# Add buttons to bar
$bar->addField(GDT_Link::make('link_account_form')->href(href('Account', 'Form'))->icon('account'));
$bar->addField(GDT_Link::make('link_settings')->href(href('Account', 'Settings'))->icon('settings'));
if ($module->cfgFeatureDeletion()) :
	$bar->addField(GDT_Link::make('link_account_delete')->href(href('Account', 'Delete'))->icon('delete'));
endif;

# Render
echo $bar->renderCell();
