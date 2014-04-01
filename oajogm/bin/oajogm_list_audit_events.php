#!/usr/bin/php
<?php

include realpath(dirname(__FILE__))."/initcli.php";

$opts = new Zend_Console_Getopt(
    array(
        'number|l-i'   => 'Number',
        'verbose|v'   => 'Verbose',
    )
);

try {
    $opts->parse();
} catch (Zend_Console_Getopt_Exception $e) {
    echo $e->getUsageMessage();
    exit(1);
}

$sm = LGC_Service_Manager::getInstance();
$gwMgr = $sm->getService('gateway_manager');

$args = array();

if($opts->getOption('number')) {
    $args['number'] = $opts->getOption('number');
}
if($opts->getOption('verbose')) {
    $verbose = true;
} else {
    $verbose = false;
}

$history = $gwMgr->listAuditEvents($args);
foreach($history as $event) {
    echo $event["timestamp"]."\t";
    echo $event["username"]."\t";
    if(!$verbose) {
        echo $event["action"]."\n";
    } else {
        echo $event["action"]."\t";
        echo $event["data"]."\n";
    }
}

exit(0);
