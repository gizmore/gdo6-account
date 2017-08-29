<?php
use GDO\Account\Module_Account;
use GDO\Template\GDT_Box;

echo Module_Account::instance()->renderAdminTabs();

$numWaitingActivation = 0;

echo GDT_Box::make()->html(t('box_content_account_admin', [$numWaitingActivation]))->render();
