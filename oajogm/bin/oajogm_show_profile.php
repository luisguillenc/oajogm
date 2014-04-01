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
$prfMgr = $sm->getService('access_profile_manager');

$args = array(
    'name' => $optName,
    );

try {
    $profile = $prfMgr->showProfile($args);
} catch(Core_Service_Exception $e) {
    echo $e->getMessage()."\n";
    exit(2);
}

if(empty($profile)) {
    echo "No se encuentra el perfil\n";
    exit(2);
}

echo "id: ".$profile['id']."\n";
echo "name: ".$profile['name']."\n";
echo "desc: ".$profile['desc']."\n";
echo "created: ".$profile['audit_info']['created']."\n";
echo "created_by: ".$profile['audit_info']['created_by']."\n";
echo "updated: ".$profile['audit_info']['updated']."\n";
echo "updated_by: ".$profile['audit_info']['updated_by']."\n";
echo "resources: \n";
foreach($profile['resources'] as $resource) {
    $line = $resource['id']."\t";
    $line.= $resource['name']."\t";
    $line.= $resource['type']."\t";
    $line.= $resource['desc']."\n";
    echo $line;
}


exit(0);
