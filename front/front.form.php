<?php
include('../../../inc/includes.php');

Html::simpleHeader("Approve", array());
$pl = new PluginApprovebylinkFront();
if (isset($_POST['update'])){
    $pl->handlePost($_POST);
}else{
    $pl->handleGetUrl($_GET);
}


