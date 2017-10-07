<?php
# Create a horizontal navbar.
use GDO\UI\GDT_Bar;
use GDO\UI\GDT_Link;

$bar = GDT_Bar::make();

# Add buttons to bar
$bar->addField(GDT_Link::make('link_account_admin')->href(href('Account', 'Admin'))->icon('admin'));
$bar->addField(GDT_Link::make('link_account_activations')->href(href('Account', 'Activations'))->icon('account_box'));
$bar->addField(GDT_Link::make('link_account_deletions')->href(href('Account', 'Deletions'))->icon('delete'));

# Render
echo $bar->renderCell();
