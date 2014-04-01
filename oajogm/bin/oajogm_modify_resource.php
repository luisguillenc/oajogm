#!/usr/bin/php
<?php

include realpath(dirname(__FILE__))."/initcli.php";

$opts = new Zend_Console_Getopt(
    array(
        'name|n=s'   => 'Name',
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
if($opts->getOption('ipaddr')) {
    $args['ipaddr'] = $opts->getOption('ipaddr');
}
if($opts->getOption('netaddr')) {
    $args['netaddr'] = $opts->getOption('netaddr');
}
if($opts->getOption('netmask')) {
    $args['netmask'] = $opts->getOption('netmask');
}
if($opts->getOption('beginip')) {
    $args['beginip'] = $opts->getOption('beginip');
}
if($opts->getOption('endip')) {
    $args['endip'] = $opts->getOption('endip');
}

$sm = LGC_Service_Manager::getInstance();
$resMgr = $sm->getService('network_resource_manager');

try {
    $resource = $resMgr->modifyResource($args);
} catch(Core_Service_Exception $e) {
    echo $e->getMessage()."\n";
    exit(2);
}

echo "Recurso modificado correctamente\n";
//var_dump($resource);

exit(0);