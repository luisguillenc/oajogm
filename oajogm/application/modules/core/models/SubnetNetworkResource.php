<?php

/**
 * Clase que almacena la información particular de los recursos de red
 * de tipo Subred
 * 
 * @category OAJogm
 * @package Core
 * @subpackage Models
 * @author luis
 */
class Core_Model_SubnetNetworkResource 
    extends Core_Model_NetworkResource
{

    /** 
     * Dirección de subred
     * 
     * @var Core_Model_NetworkAddress 
     */
    protected $_subnet;
    
    /**
     * 
     * @param string $name
     * @param Core_Model_NetworkAddress $subnet dirección de la subred
     * @param string $desc
     */
    public function __construct($name, Core_Model_NetworkAddress $subnet, $desc = "") {
        parent::__construct($name, $desc);
        $this->_subnet = $subnet;
    }
    
    /** @param Core_Model_NetworkAddress $subnet */
    public function setSubnet(Core_Model_NetworkAddress $subnet) {
        $this->_subnet = $subnet;
    }
    
    /** @return Core_Model_NetworkAddress */
    public function getSubnet() {
        return $this->_subnet;
    }
 
    /**
     * @return string
     */
    public function getType() {
        return "subnet";
    }

    /**
     * 
     * @return array
     */
    public function toArray() {
        $data = parent::toArray();
        $data['netaddr'] = $this->_subnet->getNetAddress()->__toString();
        $data['netmask'] = $this->_subnet->getNetMask()->__toString();
        
        return $data;
    }

}

