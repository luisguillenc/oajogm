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
$netResMgr = $sm->getService('network_resource_manager');

$args = array(
    'name' => $optName,
    );

$resource = $netResMgr->showResource($args);

if(empty($resource)) {
    echo "No se encuentra recurso\n";
    exit(2);
}

echo "id: ".$resource['id']."\n";
echo "name: ".$resource['name']."\n";
echo "type: ".$resource['type']."\n";
switch($resource['type']) {
    case 'host':
        echo "ip_addr: ".$resource['ip']."\n";
        break;
    case 'subnet':
        echo "net_addr: ".$resource['network_addr']."\n";
        echo "net_mask: ".$resource['network_mask']."\n";
        break;
    case 'range':
        echo "begin_ip: ".$resource['begin_ip']."\n";
        echo "end_ip: ".$resource['end_ip']."\n";
        break;
}
echo "desc: ".$resource['desc']."\n";
echo "created: ".$resource['audit_info']['created']."\n";
echo "created_by: ".$resource['audit_info']['created_by']."\n";
echo "updated: ".$resource['audit_info']['updated']."\n";
echo "updated_by: ".$resource['audit_info']['updated_by']."\n";


exit(0);
