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
$gwMgr = $sm->getService('gateway_manager');

$args = array(
    'name' => $optName,
    );

try {
    if($gwMgr->disconnectClient($args)) {
        echo "El cliente se desconectó correctamente\n";
        exit(0);
    } else {
        echo "Hubo un error al desconectar el cliente\n";
        exit(1);        
    } 
} catch(Core_Service_Exception $e) {
    echo $e->getMessage()."\n";
    exit(2);
}
