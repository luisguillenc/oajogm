#!/usr/bin/php
<?php

include realpath(dirname(__FILE__))."/initcli.php";

$opts = new Zend_Console_Getopt(
    array(
        'number|l-i'   => 'Number'
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

try {
    echo $gwMgr->viewIptablesLog($args);
    exit(0);
} catch (Core_Service_Exception $ex) {
    echo "Error: ".$ex->getMessage();
    exit(1);
}
