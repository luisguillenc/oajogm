#!/usr/bin/php
<?php

include realpath(dirname(__FILE__))."/initcli.php";

$sm = LGC_Service_Manager::getInstance();
$gwMgr = $sm->getService('gateway_manager');

$gwMgr->enableAccess();

echo "Acceso habilitado al gateway\n";

