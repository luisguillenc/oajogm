<?php

/**
 * Clase que implementará la gestión del pool de direcciones de la vpn
 * utilizando una tabla de la base de datos para almacenar las conesiones
 * 
 * @category OAJogm
 * @package Core
 * @subpackage Models
 * @author luis
 */
class Core_Model_VpnIpPoolerDb 
    implements Core_Model_VpnIpPoolerInterface
{
    /** @var Core_Model_DbTable_VpnPool */
    protected $_dbTable;
    
    /**
     * Dirección de red de la vpn
     * 
     *  @var Core_Model_NetworkAddress 
     */
    protected $_vpnNet;
    
    /**
     * Ips excluidas del rango
     * 
     * @var Core_Model_IPAddress[]
     */
    protected $_excludeIps;
    
    
    /**
     * Array asociativo con los datos en memoria del pool
     * 
     * @var array
     */
    protected $_dataPool = array();
    /**
     * 
     * @param Core_Model_NetworkAddress $vpnNet
     * @param Core_Model_IPAddress[] $excludeIps
     */
    public function __construct(Core_Model_NetworkAddress $vpnNet, $excludeIps) {
        $this->_dbTable = new Core_Model_DbTable_VpnPool();
        $this->_vpnNet = $vpnNet;
        $this->_excludeIps = $excludeIps;
    }
    
    public function initializePool() {
        $data = array();
        $data['netaddr'] = $this->_vpnNet->getNetAddress()->__toString();
        $data['netmask'] = $this->_vpnNet->getNetMask()->__toString();        
        $data['last'] = false;
        $data['reserved'] = false;
        
        $a_netaddr = $this->_vpnNet->getNetAddress()->getData();
        for($i = 1; $i<255; $i++) {
            $newIp = new Core_Model_IPAddress(
                    array($a_netaddr[0], $a_netaddr[1], $a_netaddr[2], $i)
                    );
            $excluded = false;
            foreach($this->_excludeIps as $excludeIp) {
                if($newIp == $excludeIp) {
                    $excluded = true;
                    break;
                }
            }
            if(!$excluded) {
                $data['ipaddr'] = $newIp->__toString();
                $this->_dbTable->insert($data);
            }
        }
    }
    
    protected function _loadData() {
        $select = $this->_dbTable->select()
                ->where("netaddr = ?", $this->_vpnNet->getNetAddress()->__toString())
                ->where("netmask = ?", $this->_vpnNet->getNetmask()->__toString());

        $this->_dataPool = $select->query()->fetchAll();
    }
    
    /** 
     * @return Core_Model_IPAddress 
     */
    public function lease() {
        $this->_loadData();
        
        //en la primera iteración se debe reservar si ya se ha pasado por la
        //última ip libre
        $lastip = array();
        $reservaip = array();

        foreach($this->_dataPool as $poolip) {
            if($poolip['last']) {
                $lastip = $poolip;
            } else {
                if(!$poolip['reserved']) {
                    if(!empty($lastip)) {
                        $reservaip = $poolip;
                        break;
                    }
                }
            }
        }

        //realizamos segunda iteración si no conseguimos reservar
        if(empty($reservaip)) {
            reset($this->_dataPool);
            foreach($this->_dataPool as $poolip) {
                if(!$poolip['reserved']) {
                    $reservaip = $poolip;
                    break;
                }
            }
        }
        
        if(!empty($lastip) && !empty($reservaip)) {
            $lastip['last'] = false;

            $where = $this->_dbTable->getAdapter()->quoteInto('id = ?', $lastip['id']);
            $this->_dbTable->update($lastip, $where);
        }

        if(!empty($reservaip)) {
            $reservaip['last'] = true;
            $reservaip['reserved'] = true;

            $where = $this->_dbTable
                        ->getAdapter()->quoteInto('id = ?', $reservaip['id']);
            $this->_dbTable->update($reservaip, $where);

            return new Core_Model_IPAddress($reservaip['ipaddr']);
        }
        
        return null;
    }

    /**
     * @param Core_Model_IPAddress $ip
     */
    public function release(Core_Model_IPAddress $ip) {
        $this->_loadData();
        
        $released = false;
        foreach($this->_dataPool as $poolip) {
            $compareIp = new Core_Model_IPAddress($poolip['ipaddr']);
            if($ip == $compareIp) {
                $poolip['reserved'] = false;
                $where = $this->_dbTable
                    ->getAdapter()->quoteInto('id = ?', $poolip['id']);
                $this->_dbTable->update($poolip, $where);
                $released = true;
                break;
            }
        }
        if(!$released) {
            throw new Core_Model_Exception("No se encontró la ip en el pool");
        }
    }
}

