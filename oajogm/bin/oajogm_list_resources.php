#!/usr/bin/php
<?php

include realpath(dirname(__FILE__))."/initcli.php";

$sm = LGC_Service_Manager::getInstance();
$netResMgr = $sm->getService('network_resource_manager');

$resources = $netResMgr->listResources();
foreach($resources as $resource) {
    $line = $resource['id']."\t";
    $line.= $resource['name']."\t";
    $line.= $resource['type']."\t";
    $line.= $resource['desc']."\n";
    echo $line;
}

exit(0);
