#!/usr/bin/php
<?php

include realpath(dirname(__FILE__))."/initcli.php";

$opts = new Zend_Console_Getopt(
    array(
        'name|n=s'   => 'Name',
    )
);

try {
    $opts->parse();
} catch (Zend_Console_Getopt_Exception $e) {
    echo $e->getUsageMessage();
    exit(1);
}


$optName = $opts->getOption('name');
if(!$optName) {
    echo $opts->getUsageMessage();
    echo "Falta parámetro name\n";
    exit(1);
}

$sm = LGC_Service_Manager::getInstance();
$cliMgr = $sm->getService('access_client_manager');

$args = array(
    'name' => $optName,
    );

try {
    $client = $cliMgr->showClient($args);
} catch(Core_Service_Exception $e) {
    echo $e->getMessage()."\n";
    exit(2);
}

if(empty($client)) {
    echo "No se encuentra el cliente\n";
    exit(2);
}

echo "name: ".$client['name']."\n";
echo "desc: ".$client['desc']."\n";
echo "profile: ".$client['prfname']."\n";
echo "ipaddr: ".$client['ipaddr']."\n";
if($client['locked']) {
    echo "locked: yes\n";
} else {
    echo "locked: no\n";
}
if($client['iptableslogged']) {
    echo "iptableslogged: yes\n";
} else {
    echo "iptableslogged: no\n";
}
echo "created: ".$client['audit_info']['created']."\n";
echo "created_by: ".$client['audit_info']['created_by']."\n";
echo "updated: ".$client['audit_info']['updated']."\n";
echo "updated_by: ".$client['audit_info']['updated_by']."\n";


exit(0);
