#!/usr/bin/php
<?php

include realpath(dirname(__FILE__))."/initcli.php";

$sm = LGC_Service_Manager::getInstance();
$prfMgr = $sm->getService('access_profile_manager');

$profiles = $prfMgr->listProfiles();
foreach($profiles as $profile) {
    $line= $profile['name']."\t";
    $line.= $profile['desc']."\n";
    echo $line;
}

exit(0);
