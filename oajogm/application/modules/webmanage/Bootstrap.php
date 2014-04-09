<?php

class Webmanage_Bootstrap extends Zend_Application_Module_Bootstrap {

    protected function _initMenus() { 
        $cfg_Menus = new Zend_Config_Ini(APPLICATION_PATH.'/configs/menus_webmanage.ini');
        $cfg_Menus->readOnly();
        Zend_Registry::set("menusCfg", $cfg_Menus);

        return true;
    }

}
