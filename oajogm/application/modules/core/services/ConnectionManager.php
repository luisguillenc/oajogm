<?php

/**
 * Clase de servicio que proporciona una interfaz con la que se comunicarán
 * los componentes del gateway
 * 
 * @category OAJogm
 * @package Core
 * @subpackage Services
 * @author luis
 */
class Core_Service_ConnectionManager {

    /** @var Core_Model_GatewayConfiguration */
    protected $_gateway;
    
    /** @var Core_Model_AccessClientFinderInterface */
    protected $_clientFinder;
    
    /** @var Core_Model_ConnectionLoggerInterface */
    protected $_cnLogger;
    
    /**
     * 
     * @param Core_Model_GatewayConfiguration $gateway
     * @param Core_Model_AccessClientFinderInterface $clientFinder
     * @param Core_Model_ConnectionLoggerInterface $cnLogger
     */
    public function __construct(
            Core_Model_GatewayConfiguration $gateway,
            Core_Model_AccessClientFinderInterface $clientFinder,
            Core_Model_ConnectionLoggerInterface $cnLogger
            ) {
        $this->_gateway = $gateway;
        $this->_clientFinder = $clientFinder;
        $this->_cnLogger = $cnLogger;
    }
    
    
    /**
     * 
     * @param type $name
     * @return Core_Model_AccessClient
     */
    protected function _findClient($name) {
        $client = $this->_clientFinder->findByName($name);
        if(empty($client)) {
            throw new Core_Service_Exception("No existe el cliente $name");
        }
        
        return $client;
    }

    /**
     * @param string $name
     */
    public function notifyConnect($name) {
        $this->_cnLogger->connect($name);
    }

    /**
     * @param string $name
     */
    public function notifyDisconnect($name) {
        $this->_cnLogger->disconnect($name);
    }
    
    /**
     * @param string $name
     * @return boolean si tiene acceso
     */
    public function checkAccess($name) {
        if(!$this->_gateway->getAccessStatus()) {
            return false;
        }
        $client = $this->_findClient($name);
        if($client->isLocked()) {
            return false;
        }
        
        return true;
    }

    /**
     * @param string $name
     * @return string
     */
    public function getClientConfigScript($name) {
        $client = $this->_findClient($name);
        $vpnIp = $client->getVpnIp();
        $networks = $this->_gateway->getRoutedNetworks();

        $configScript = "ifconfig-push $vpnIp 255.255.255.0\n";
        foreach($networks as $network) {
            $netaddr = $network->getNetAddress();
            $netmsk = $network->getNetMask();

            $configScript.="push 'route $netaddr $netmsk'\n";
        }
        
        return $configScript;
    }
    
    /**
     * @return string
     */
    public function getIptablesScript() {
        $iptCompiler = new Core_Model_IptablesCompiler(
                    $this->_gateway, $this->_clientFinder
                );
        return $iptCompiler->getScript();
    }


}
