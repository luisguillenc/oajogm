<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

    public function _initConfig() {
        $pathsCfg = new Zend_Config_Ini('paths.ini', 'default');
        $gatewayCfg = new Zend_Config_Ini('gateway.ini', 'gateway');
        
        Zend_Registry::set("pathsCfg", $pathsCfg);
        Zend_Registry::set("gatewayCfg", $gatewayCfg);
        
        $this->bootstrap('db');
    }
    
    
}

