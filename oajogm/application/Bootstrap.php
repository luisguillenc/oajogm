<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

    public function _initConfig() {
        $pathsCfg = new Zend_Config_Ini('paths.ini', 'paths');
        $gatewayCfg = new Zend_Config_Ini('gateway.ini', 'gateway');
        $operatorCfg = new Zend_Config_Ini('operator.ini', 'operator');
        
        Zend_Registry::set("pathsCfg", $pathsCfg);
        Zend_Registry::set("gatewayCfg", $gatewayCfg);
        Zend_Registry::set("operatorCfg", $operatorCfg);
        
        
        $this->bootstrap('db');
    }
    
    protected function _initLocale() {
	date_default_timezone_set('Europe/Madrid');
	Zend_Locale::setLocale('es_ES');
    }

    protected function _initModularLayout() {
        $this->bootstrap('frontController');
        $front = Zend_Controller_Front::getInstance();
        $front->registerPlugin(
            new LGC_Controller_Plugin_Modularlayout()
        );
    }

}

