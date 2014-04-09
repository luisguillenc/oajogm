#!/usr/bin/php
<?php

include realpath(dirname(__FILE__))."/initcli.php";

$sm = LGC_Service_Manager::getInstance();
$gwMgr = $sm->getService('gateway_manager');

$status = $gwMgr->status();

echo "service_ip: ".$status['service_ip']."\n";
echo "vpn_network: ".$status['vpn_network']."\n";
echo "routed_networks:";
foreach($status['routed_networks'] as $routed_net) {
    echo " $routed_net";
}
echo "\n";
if($status['access_status']) {
    echo "access_status: enabled\n";
} else {
    echo "access_status: disabled\n";
}

if($status['vpn_service']) {
    echo "vpn_service: started\n";
} else {
    echo "vpn_service: stoped\n";
}

