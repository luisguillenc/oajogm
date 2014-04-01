<?php

/**
 * Clase que implementa la factoría con la que se deberán crear los recursos 
 * de red. Esta factoría tendrá la responsabilidad de garantizar que 
 * el nombre del recurso de red no se encuentra en el repositorio.
 * 
 * @category OAJogm
 * @package Core
 * @subpackage Models
 * @author luis
 */
class Core_Model_NetworkResourceFactory {
    
    /**  @var Core_Model_NetworkResourceFinderInterface */
    protected $_finder;
    
    
    /**
     * @param Core_Model_NetworkResourceFinderInterface $finder
     */
    public function __construct(
            Core_Model_NetworkResourceFinderInterface $finder
            ) {
        $this->_finder = $finder;
    }
    
    /**
     * Método de creación de recurso de tipo Host
     * 
     * @param string $name
     * @param Core_Model_IPAddress $ip ip del host
     * @return Core_Model_HostNetworkResource
     * @throws Core_Model_Exception si existe el recurso con el nombre
     */
    public function createHost($name, Core_Model_IPAddress $ip) {
        $result = $this->_finder->findByName($name);
        if(!empty($result)) {
            throw new Core_Model_Exception("Ya existe recurso con ese nombre");
        }
        
        return new Core_Model_HostNetworkResource($name, $ip);
    }

    /**
     * Método de creación de recurso de tipo subred
     * 
     * @param string $name
     * @param Core_Model_NetworkAddress $subnet dirección de la subred
     * @return Core_Model_SubnetNetworkResource
     * @throws Core_Model_Exception si existe recurso con el nombre
     */
    public function createSubnet($name, Core_Model_NetworkAddress $subnet) {
        $result = $this->_finder->findByName($name);
        if(!empty($result)) {
            throw new Core_Model_Exception("Ya existe recurso con ese nombre");
        }
        
        return new Core_Model_SubnetNetworkResource($name, $subnet);
    }

    /**
     * Método de creación de recurso de tipo range
     * 
     * @param string $name
     * @param Core_Model_IPAddress $beginIp ip de inicio del rango
     * @param Core_Model_IPAddress $endIp ip de fin del rango
     * @return Core_Model_RangeNetworkResource
     * @throws Core_Model_Exception si existe recurso con el nombre
     */
    public function createRange(
            $name, 
            Core_Model_IPAddress $beginIp, Core_Model_IPAddress $endIp
        ) 
    {
        $result = $this->_finder->findByName($name);
        if(!empty($result)) {
            throw new Core_Model_Exception("Ya existe recurso con ese nombre");
        }
        
        return new Core_Model_RangeNetworkResource($name, $beginIp, $endIp);
    }
    
}
