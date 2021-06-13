<?php
use GDO\Account\Module_Account;
use GDO\UI\GDT_Panel;

echo Module_Account::instance()->renderAdminTabs();

$numWaitingActivation = 0;

echo GDT_Panel::make()->text('box_content_account_admin', [$numWaitingActivation])->render();
