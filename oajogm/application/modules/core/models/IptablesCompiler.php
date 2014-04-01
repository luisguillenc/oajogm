<?php

/**
 * Clase que implementa el compilador iptables que genera el script adecuado 
 * al repositorio de clientes del momento de la ejecución
 * 
 * @category OAJogm
 * @package Core
 * @subpackage Models
 * @author luis
 */
class Core_Model_IptablesCompiler {
    
    /**
     * Path a la plantilla usada por el compilador
     * 
     * @var string
     */
    static protected $_template;

    /** @var Core_Model_GatewayConfiguration */
    protected $_gwConf;
    
    /** @var Core_Model_AccessClientFinderInterface */
    protected $_clientFinder;
    
    /** @var Core_Model_AccessProfile[] */
    protected $_profiles = array();
    
    /** @var Core_Model_AccessClient[] */
    protected $_clients = array();

    /**
     * Configura la plantilla del compilador con el path al template pasado
     * 
     * @param string $templatePath
     */
    static public function setTemplate($templatePath) {
        self::$_template = $templatePath;
    }
    
    /**
     * 
     * @param Core_Model_GatewayConfiguration $gwConfig
     * @param Core_Model_AccessClientFinderInterface $finder
     */
    public function __construct(
            Core_Model_GatewayConfiguration $gwConfig,
            Core_Model_AccessClientFinderInterface $finder) {
        $this->_gwConf = $gwConfig;
        $this->_clientFinder = $finder;
    }
    
    protected function _getIptParams(Core_Model_NetworkResource $resource) {
        $params = "";
        if($resource instanceof Core_Model_HostNetworkResource) {
            $params = "-d ".$resource->getIp();
        }
        if($resource instanceof Core_Model_SubnetNetworkResource) {
            $params = "-d ".$resource->getSubnet();
        }
        if($resource instanceof Core_Model_RangeNetworkResource) {
            $params = "-m iprange --dst-range ".
                    $resource->getBeginIp()."-".$resource->getEndIp();
        }
        
        return $params;
    }
    
    protected function _fillArrays() {
        $clients = $this->_clientFinder->findAll();
        foreach($clients as $client) {
            $clientName = $client->getName();
            $profileName = $client->getProfile()->getName();
            
            $this->_profiles[$profileName] = $client->getProfile();
            $this->_clients[$clientName] = $client;
        }
    }

    /**
     * 
     * @TODO agregar módulo php verificador de errores de compilación
     * @return string
     */    
    public function getScript() {
        $this->_fillArrays();
        ob_start();
        try {
            include self::$_template;
            $scriptSource = ob_get_contents();
            ob_end_clean();
        } catch (Exception $e) {
            ob_clean();
            throw new Core_Model_Exception("Error al compilar ".self::$_template);
        }

        return $scriptSource;
    }
    
}