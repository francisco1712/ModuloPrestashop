<?php

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');

$module_name = 'francisco';

if (Tools::encrypt($module_name.'/ajax.php') != Tools::getValue('token') || !Module::isInstalled($module_name)) {
    die('Bad token');
}

$module = Module::getInstanceByName($module_name);
if ($module ->active) {
    $action = pSQL(Tools::getValue('action'));
}