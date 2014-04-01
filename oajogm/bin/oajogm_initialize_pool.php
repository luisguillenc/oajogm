#!/usr/bin/php
<?php

include realpath(dirname(__FILE__))."/initcli.php";

$opts = new Zend_Console_Getopt(
    array(
        'commit|c'   => 'Commit',
    )
);

try {
    $opts->parse();
} catch (Zend_Console_Getopt_Exception $e) {
    echo $e->getUsageMessage();
    exit(1);
}


if(!$opts->getOption('commit')) {
    echo $opts->getUsageMessage();
    echo "Debe usar parámetro -commit\n";
    exit(1);
}



$sm = LGC_Service_Manager::getInstance();
$vpnIpPooler = $sm->getService('vpn_ip_pooler');

$vpnIpPooler->initializePool();

exit(0);
