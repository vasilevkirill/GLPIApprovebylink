<?php

define('GLPI_ROOT', '../..');
include (GLPI_ROOT . "/inc/includes.php");
$n = new PluginApprovebylinkConfig();
Session::checkRight("config", UPDATE);

// To be available when plugin in not activated
Plugin::load('approvebylink');

Html::header("approvebylink", $_SERVER['PHP_SELF'], "config", "plugins");
echo __("This is the plugin config page", 'approvebylink');
$n->showFormExample();
Html::footer();
