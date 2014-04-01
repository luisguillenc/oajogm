#!/usr/bin/php
<?php

include realpath(dirname(__FILE__))."/initcli.php";

$opts = new Zend_Console_Getopt(
    array(
        'prfname|p=s'   => 'Profile name',
        'rscname|r=s'   => 'Resource name'
    )
);

try {
    $opts->parse();
} catch (Zend_Console_Getopt_Exception $e) {
    echo $e->getUsageMessage();
    exit(1);
}

if(!$opts->getOption('prfname') || !$opts->getOption('rscname')) {
echo $opts->getUsageMessage();
    echo "Falta parámetro requerido\n";
    exit(1);
}

$args = array();
$args['prfname'] = $opts->getOption('prfname');
$args['rscname'] = $opts->getOption('rscname');

$sm = LGC_Service_Manager::getInstance();
$prfMgr = $sm->getService('access_profile_manager');

try {
    $profile = $prfMgr->addResource($args);
} catch(Core_Service_Exception $e) {
    echo $e->getMessage()."\n";
    exit(2);
}

echo "Recurso agregado a perfil correctamente\n";
//var_dump($resource);

exit(0);