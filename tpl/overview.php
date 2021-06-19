<?php
use GDO\Account\Module_Account;
use GDO\UI\GDT_Bar;
use GDO\UI\GDT_Link;
use GDO\Account\Method\Settings;

$module = Module_Account::instance();

# Create a horizontal navbar.
$bar = GDT_Bar::make()->horizontal();

$links = [];
$links[] = GDT_Link::make('link_account_form')->href(href('Account', 'Form'))->icon('account');
if ($module->cfgFeatureDeletion()) :
	$links[] = GDT_Link::make('link_account_delete')->href(href('Account', 'Delete'))->icon('delete');
endif;

$links = array_merge($links, Settings::make()->navLinks());

usort($links, function(GDT_Link $one, GDT_Link $two) {
    return strcasecmp($one->displayLabel(), $two->displayLabel());
});
    
    
# Add buttons to bar
$bar->addFields($links);

# Render
echo $bar->renderCell();
