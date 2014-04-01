#!/usr/bin/php
<?php

include realpath(dirname(__FILE__))."/initcli.php";

$sm = LGC_Service_Manager::getInstance();
$gwMgr = $sm->getService('gateway_manager');

if($gwMgr->stop()) {
    echo "Gateway detenido con éxito\n";
    exit(0);
} else {
    echo "Error al detener el gateway\n";
    exit(1);
}

