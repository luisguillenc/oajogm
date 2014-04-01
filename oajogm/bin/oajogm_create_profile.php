#!/usr/bin/php
<?php

include realpath(dirname(__FILE__))."/initcli.php";

$opts = new Zend_Console_Getopt(
    array(
        'name|n=s'   => 'Name',
        'desc|d-s'   => 'Description'
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
if($opts->getOption('desc')) {
    $args['desc'] = $opts->getOption('desc');
}

$sm = LGC_Service_Manager::getInstance();
$prfMgr = $sm->getService('access_profile_manager');

try {
    $profile = $prfMgr->createProfile($args);
} catch(Core_Service_Exception $e) {
    echo $e->getMessage()."\n";
    exit(2);
}

echo "Perfil creado correctamente\n";
//var_dump($resource);

exit(0);