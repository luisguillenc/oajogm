<?php

class Webmanage_View_Helper_IconMgr extends Zend_View_Helper_Abstract
{
    protected $_iconHashMap = null;
    protected $_iconsPath = "/images/icons";
    protected $_baseUrl = null;

    /**
     * Display Flash Messages.
     *
     * @param  string $key Message level for string messages
     * @param  string $template Format string for message output
     * @return string Flash messages formatted for output
     */
    public function iconMgr($name)
    {
        if(null === $this->_baseUrl) {
            $helperBase = new Zend_View_Helper_BaseUrl();
            $this->_baseUrl = $helperBase->baseUrl();
        }
        
        $this->_getIconHashMap();
        $icon = $this->_iconHashMap[$name];
        if($icon) {
            return '<img src="'.$this->_baseUrl.$this->_iconsPath.'/'.$icon[0].'" alt="'.$icon[1].'" border="0">';
        }
        return "";

    }
    
    public function _getIconHashMap()
    {
        if (null === $this->_iconHashMap) {
            $this->_iconHashMap = array();
            $this->_iconHashMap['NEW'] = array("boton_nuevo.png", "Crear");
            $this->_iconHashMap['UPDATE'] = array("boton_editar.png", "Editar");
            $this->_iconHashMap['DELETE'] = array("boton_borrar.png", "Eliminar");
            $this->_iconHashMap['BACK'] = array("boton_volver.png", "Volver");
            $this->_iconHashMap['VIEW_BUTTON'] = array("boton_ver.png", "Ver");

            $this->_iconHashMap['RESOURCE'] = array("network_resource.png", "Resource");
            $this->_iconHashMap['PROFILE'] = array("access_profile.png", "Profile");
            $this->_iconHashMap['CLIENT'] = array("access_client.png", "Client");
            $this->_iconHashMap['GATEWAY'] = array("gateway.png", "Gateway");
            $this->_iconHashMap['VIEW_LOG'] = array("view_log.png", "View log");
            $this->_iconHashMap['DISCONNECT_CLIENT'] = array("disconnect_client.png", "Desconectar cliente");
        }
        return $this->_iconHashMap;
    }

}