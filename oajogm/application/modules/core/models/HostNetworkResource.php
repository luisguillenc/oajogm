<?php

/**
 * Clase que almacena la información particular de los recursos de red
 * de tipo Host
 * 
 * @category OAJogm
 * @package Core
 * @subpackage Models
 * @author luis
 */
class Core_Model_HostNetworkResource 
    extends Core_Model_NetworkResource
{

    /** 
     * Dirección ip del recurso de red
     * @var Core_Model_IPAddress 
     */
    protected $_ip;
    
    /**
     * 
     * @param string $name
     * @param Core_Model_IPAddress $ip
     * @param string $desc
     */
    public function __construct($name, Core_Model_IPAddress $ip, $desc = "") {
        parent::__construct($name, $desc);
        $this->_ip = $ip;
    }
    
    /** @param Core_Model_IPAddress $ip */
    public function setIp(Core_Model_IPAddress $ip) {
        $this->_ip = $ip;
    }
    
    /** @return Core_Model_IPAddress */
    public function getIp() {
        return $this->_ip;
    }

    /**
     * @return string
     */
    public function getType() {
        return "host";
    }
    
    /**
     * @return array
     */
    public function toArray() {
        $data = parent::toArray();
        $data['ipaddr'] = $this->_ip->__toString();
        
        return $data;
    }
    
}

