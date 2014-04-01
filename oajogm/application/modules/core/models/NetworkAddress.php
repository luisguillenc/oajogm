<?php

/**
 * Clase que almacena la información de una dirección de red IPv4
 * 
 * @category OAJogm
 * @package Core
 * @subpackage Models
 * @author luis
 */
class Core_Model_NetworkAddress {

    /** @var Core_Model_IPAddress */
    private $_netAddress;

    /** @var Core_Model_IPAddress */
    private $_netMask;
    
    /**
     * @param Core_Model_IPAddress $netAddress
     * @param Core_Model_IPAddress $netMask
     */
    public function __construct(
            Core_Model_IPAddress $netAddress, Core_Model_IPAddress $netMask
            ) {
        //TODO: chequear que es una dirección de red correcta
        // throw new Core_Model_Exception("Dirección de red no válida")
            $this->_netAddress = $netAddress;
            $this->_netMask = $netMask;
    }
    
    /**
     * @return Core_Model_IPAddress
     */
    public function getNetAddress() {
       return $this->_netAddress; 
    }
    
    /**
     * @return Core_Model_IPAddress
     */    
    public function getNetMask() {
        return $this->_netMask;
    }

    /**
     * Comprueba si la dirección ip pasada pertenece al rango de la red
     * 
     * @param Core_Model_IPAddress $ip
     * @return boolean
     */
    public function isValid(Core_Model_IPAddress $ip) {
        // algoritmo extraído de:
        // http://stackoverflow.com/questions/8594894/check-whether-an-ip-address-is-in-a-network
        $ipaddr = ip2long($ip->__toString());
        $netip = ip2long($this->_netAddress->__toString());
        $netmask = ip2long($this->_netMask->__toString());
        
        return (($ipaddr & $netmask) == ($netip & $netmask));
    }
    
    /**
     * Representación en string de la dirección de red
     * 
     * @return string
     */
    public function __toString() {
        $str = $this->_netAddress->__toString()."/";
        $str.= $this->_netMask->__toString();
        
        return $str;
    }
}

