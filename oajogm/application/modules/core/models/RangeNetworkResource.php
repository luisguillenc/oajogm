<?php

/**
 * Clase que almacena la información particular de los recursos de red
 * de tipo Rango
 * 
 * @category OAJogm
 * @package Core
 * @subpackage Models
 * @author luis
 */
class Core_Model_RangeNetworkResource 
    extends Core_Model_NetworkResource
{

    /** 
     * Dirección ip del inicio del rango
     * 
     * @var Core_Model_IPAddress 
     */
    protected $_begin;

    /**
     * Dirección ip de fin del rango
     * @var Core_Model_IPAddress 
     */
    protected $_end;

    /**
     * 
     * @param string $name
     * @param Core_Model_IPAddress $begin ip de inicio de rango
     * @param Core_Model_IPAddress $end ip de fin de rango
     * @param string $desc
     */
    public function __construct(
            $name, Core_Model_IPAddress $begin, Core_Model_IPAddress $end,
            $desc = "") {
        parent::__construct($name, $desc);
        $this->_begin = $begin;
        $this->_end = $end;
    }
    
    /** @param Core_Model_IPAddress $begin */
    public function setBeginIp(Core_Model_IPAddress $begin) {
        $this->_begin = $begin;
    }
    
    /** @return Core_Model_IPAddress */
    public function getBeginIp() {
        return $this->_begin;
    }

    /** @param Core_Model_IPAddress $end */
    public function setEndIp(Core_Model_IPAddress $end) {
        $this->_end = $end;
    }
    
    /** @return Core_Model_IPAddress */
    public function getEndIp() {
        return $this->_end;
    }
    
    
    /**
     * @return string
     */
    public function getType() {
        return "range";
    }

    /**
     * @return array
     */
    public function toArray() {
        $data = parent::toArray();
        $data['begin_ip'] = $this->_begin->__toString();
        $data['end_ip'] = $this->_end->__toString();
        
        return $data;
    }

}

