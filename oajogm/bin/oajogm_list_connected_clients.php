#!/usr/bin/php
<?php

include realpath(dirname(__FILE__))."/initcli.php";

$sm = LGC_Service_Manager::getInstance();
$gwMgr = $sm->getService('gateway_manager');

$clientes = $gwMgr->showClients();

foreach($clientes as $cliente) {
    echo $cliente["cn"]."\t";
    echo $cliente["real_address"]."\t";
    echo $cliente["bytes_received"]."\t";
    echo $cliente ["bytes_sent"]."\t";
    echo $cliente["connected_since"]."\n";

}
