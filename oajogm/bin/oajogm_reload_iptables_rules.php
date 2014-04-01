#!/usr/bin/php
<?php

include realpath(dirname(__FILE__))."/initcli.php";

$sm = LGC_Service_Manager::getInstance();
$gwMgr = $sm->getService('gateway_manager');

if($gwMgr->reloadIptablesRules()) {
    echo "Reglas recargadas con éxito\n";
    exit(0);
} else {
    echo "Error al recargar las reglas\n";
    exit(1);
}

