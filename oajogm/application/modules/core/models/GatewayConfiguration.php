<?php

/**
 * Clase que almacena la información de configuración del Gateway.
 * Ofrecerá además la posibilidad de activar o desactivar los accesos al gateway.
 * 
 * @category OAJogm
 * @package Core
 * @subpackage Models
 * @author luis
 */
class Core_Model_GatewayConfiguration 
{
    
    /** 
     * Dirección ip de servicio del gateway
     * 
     * @var Core_Model_IPAddress 
     */
    protected $_serviceIp;

    /**
     * Dirección de red de la red vpn
     *  
     * @var Core_Model_NetworkAddress 
     */
    protected $_vpnNetwork;

    /**
     * Array con las rutas publicadas por el gateway
     * 
     * @var Core_Model_Network[] 
     */
    protected $_routedNetworks;

    /**
     * Clave compartida para autenticación de webservice
     * @var string
     */
    protected $_gwSharedKey;
    
    /**
     * Ruta al fichero que se empleará para almacenar el estado de acceso
     * al gateway
     * 
     * @var string
     */
    protected static $_lockFile = "";
    
    /**
     * @param string $lockFile con la ruta al fichero
     */
    public static function setLockFilePath($lockFile) {
        self::$_lockFile = $lockFile;
    }
    
    /**
     * 
     * @param Core_Model_IPAddress $serviceIp
     * @param Core_Model_NetworkAddress $vpnNetwork
     * @param Core_Model_NetworkAddress[] $routedNetworks
     * @param string $gwSharedKey
     */
    public function __construct(
            Core_Model_IPAddress $serviceIp, Core_Model_NetworkAddress $vpnNetwork,
            $routedNetworks, $gwSharedKey = ""
            ) {
        $this->_serviceIp = $serviceIp;
        $this->_vpnNetwork = $vpnNetwork;
        $this->_routedNetworks = $routedNetworks;
        $this->_gwSharedKey = $gwSharedKey;
    }

    /** @return Core_Model_IPAddress */
    public function getServiceIp() {
        return $this->_serviceIp;
    }
    
    /** @return Core_Model_NetworkAddress */
    public function getVpnNetwork() {
        return $this->_vpnNetwork;
    }
    
    /** @var Core_Model_Network[] */
    public function getRoutedNetworks() {
        return $this->_routedNetworks;
    }

    public function enableAccess() {
        if(self::$_lockFile == "") {
            throw new Core_Model_Exception("No se ha definido lockfile!");
        }
        
        if(file_exists(self::$_lockFile)) {
            unlink(self::$_lockFile);
        }
    }
    
    public function disableAccess() {
        if(self::$_lockFile == "") {
            throw new Core_Model_Exception("No se ha definido lockfile!");
        }

        touch(self::$_lockFile);
    }

    /** 
     * Si está habilitado, retorna cierto
     * @return boolean 
     */
    public function getAccessStatus() {
        if(self::$_lockFile == "") {
            throw new Core_Model_Exception("No se ha definido lockfile!");
        }

        return !file_exists(self::$_lockFile);
    }
    

    /**
     * Devuelve cierto si se ha habilitado clave compartida 
     * @return boolean
     */
    public function isGatewaySharedKeyEnabled() {
        return ($this->_gwSharedKey != "");
    }

    /**
     * @param type $salt
     * @param type $hashMd5
     * @return boolean
     */
    public function checkGatewaySharedKey($salt, $hashMd5) {
        $check = md5($salt.$this->_gwSharedKey);
        return ($hashMd5 == $check);
    }
}
