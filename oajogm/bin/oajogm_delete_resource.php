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

if(!$opts->getOption('name')) {
echo $opts->getUsageMessage();
    echo "Falta parámetro requerido\n";
    exit(1);
}

$args = array();
$args['name'] = $opts->getOption('name');

$sm = LGC_Service_Manager::getInstance();
$resMgr = $sm->getService('network_resource_manager');

try {
    $resource = $resMgr->removeResource($args);
} catch(Core_Service_Exception $e) {
    echo $e->getMessage()."\n";
    exit(2);
}

echo "Recurso borrado correctamente\n";
//var_dump($resource);

exit(0);