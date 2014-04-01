#!/usr/bin/php
<?php

include realpath(dirname(__FILE__))."/initcli.php";

$opts = new Zend_Console_Getopt(
    array(
        'name|n=s'   => 'Name',
        'type|t=s'   => 'Type of network resource',
        'desc|d-s'   => 'Description',
        'ipaddr|ip-s'   => 'Host ip',
        'netaddr|na-s'   => 'Subnet addr',
        'netmask|nm-s'   => 'Subnet mask',
        'beginip|bi-s'   => 'Begin ip range',
        'endip|ei-s'   => 'End ip range',
    )
);

try {
    $opts->parse();
} catch (Zend_Console_Getopt_Exception $e) {
    echo $e->getUsageMessage();
    exit(1);
}

if(!$opts->getOption('name') || !$opts->getOption('type')) {
echo $opts->getUsageMessage();
    echo "Falta parámetro requerido\n";
    exit(1);
}
$args = array();
$args['name'] = $opts->getOption('name');
$args['type'] = $opts->getOption('type');
if($opts->getOption('desc')) {
    $args['desc'] = $opts->getOption('desc');
}

switch($args['type']) {
    case 'host':
            if(!$opts->getOption('ipaddr')) {
                echo "Falta parámetro ipaddr\n";
                exit(2);
            }
            $args['ipaddr'] = $opts->getOption('ipaddr');
        break;
    case 'subnet':
            if(!$opts->getOption('netaddr')) {
                echo "Falta parámetro netaddr\n";
                exit(2);
            }
            if(!$opts->getOption('netmask')) {
                echo "Falta parámetro netmask\n";
                exit(2);
            }
            
            $args['netaddr'] = $opts->getOption('netaddr');
            $args['netmask'] = $opts->getOption('netmask');
        break;
    case 'range':
            if(!$opts->getOption('beginip')) {
                echo "Falta parámetro beginip\n";
                exit(2);
            }
            if(!$opts->getOption('endip')) {
                echo "Falta parámetro endip\n";
                exit(2);
            }
            
            $args['beginip'] = $opts->getOption('beginip');
            $args['endip'] = $opts->getOption('endip');
        break;
    default:
        echo "Tipo desconocido!\n";
        exit(2);
}

$sm = LGC_Service_Manager::getInstance();
$resMgr = $sm->getService('network_resource_manager');

try {
    $resource = $resMgr->createResource($args);
} catch(Core_Service_Exception $e) {
    echo $e->getMessage()."\n";
    exit(2);
}

echo "Recurso creado correctamente\n";
//var_dump($resource);

exit(0);