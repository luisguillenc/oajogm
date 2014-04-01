#!/usr/bin/php
<?php

include realpath(dirname(__FILE__))."/initcli.php";

$opts = new Zend_Console_Getopt(
    array(
        'name|n=s'   => 'Name',
        'number|l-i'   => 'Number'
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
$gwMgr = $sm->getService('gateway_manager');

$args = array();
$args['name'] = $optName;
if($opts->getOption('number')) {
    $args['number'] = $opts->getOption('number');
}
    
$history = $gwMgr->listClientHistory($args);
foreach($history as $event) {
    echo $event["name"]."\t";
    echo $event["action"]."\t";
    echo $event["timestamp"]."\n";
}

exit(0);
