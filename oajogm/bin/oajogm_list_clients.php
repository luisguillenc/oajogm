#!/usr/bin/php
<?php

include realpath(dirname(__FILE__))."/initcli.php";

$sm = LGC_Service_Manager::getInstance();
$cliMgr = $sm->getService('access_client_manager');

$clients = $cliMgr->listClients();
foreach($clients as $client) {
    $line= $client['name']."\t";
    $line.= $client['prfname']."\t";
    $line.= $client['ipaddr']."\t";
    if($client['locked']) {
        $line.= "yes\t";
    } else {
        $line.= "no\t";
    }
    if($client['iptableslogged']) {
        $line.= "yes\t";
    } else {
        $line.= "no\t";
    }
    $line.= $client['desc']."\n";
    
    echo $line;
}

exit(0);
