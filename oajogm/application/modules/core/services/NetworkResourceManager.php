<?php

/**
 * Clase de servicio que proporciona una interfaz de gestión de los recursos
 * de red
 * 
 * @category OAJogm
 * @package Core
 * @subpackage Services
 * @author luis
 */
class Core_Service_NetworkResourceManager {

    /** @var Core_Model_NetworkResourceRepositoryInterface */
    protected $_repository;

    /** @var Core_Model_NetworkResourceFactory */
    protected $_factory;

    /** @var Core_Model_AccessProfileFinderInterface */
    protected $_profileFinder;
    
    /**
     * 
     * @param Core_Model_NetworkResourceRepositoryInterface $repository
     * @param Core_Model_NetworkResourceFactory $factory
     * @param Core_Model_AccessProfileFinderInterface $profileFinder
     */
    public function __construct(
            Core_Model_NetworkResourceRepositoryInterface $repository,
            Core_Model_NetworkResourceFactory $factory,
            Core_Model_AccessProfileFinderInterface $profileFinder
            ) {
        $this->_repository = $repository;
        $this->_factory = $factory;
        $this->_profileFinder = $profileFinder;
    }
    
    /**
     * 
     * @param string $name
     * @param string $ip
     * @param string $desc
     * @return Core_Model_HostNetworkResource
     */
    protected function _createHost($name, $ip, $desc = "") {
        if($this->_existsName($name)) {
            throw new Core_Service_Exception("Ya existe un recurso con ese nombre");
        }

        $ipAddr = new Core_Model_IPAddress($ip);

        $host = $this->_factory->createHost($name, $ipAddr);
        $host->setDesc($desc);
        Core_Model_AuditHelper::createInfo($host);
        $this->_repository->persist($host);
        
        Core_Model_AuditHelper::log("Crear recurso host" , $host->toArray());
        
        return $host;
    }
    
    /**
     * 
     * @param string $name
     * @param string $networkAddress
     * @param string $netmask
     * @param string $desc
     * @return Core_Model_SubnetNetworkResource
     */
    protected function _createSubnet($name, $networkAddress, $netmask, $desc = "") {
        if($this->_existsName($name)) {
            throw new Core_Service_Exception("Ya existe un recurso con ese nombre");
        }
                
        $netaddr = new Core_Model_IPAddress($networkAddress);
        $netmsk = new Core_Model_IPAddress($netmask);
        $network = new Core_Model_NetworkAddress($netaddr, $netmsk);
        
        $subnet = $this->_factory->createSubnet($name, $network, $desc);
        $subnet->setDesc($desc);
        Core_Model_AuditHelper::createInfo($subnet);
        $this->_repository->persist($subnet);
        
        Core_Model_AuditHelper::log("Crear recurso subnet" , $subnet->toArray());
        
        return $subnet;
    }
    
    /**
     * 
     * @param string $name
     * @param string $beginIp
     * @param string $endIp
     * @return Core_Model_RangeNetworkResource
     */    
    protected function _createRange($name, $beginIp, $endIp, $desc = "") {
        if($this->_existsName($name)) {
            throw new Core_Service_Exception("Ya existe un recurso con ese nombre");
        }

        $beginIp = new Core_Model_IPAddress($beginIp);
        $endIp = new Core_Model_IPAddress($endIp);

        $range = $this->_factory->createRange($name, $beginIp, $endIp, $desc);
        $range->setDesc($desc);
        Core_Model_AuditHelper::createInfo($range);
        $this->_repository->persist($range);
        
        Core_Model_AuditHelper::log("Crear recurso range" , $range->toArray());
        
        return $range;
    }

    /**
     * 
     * @param string $name
     * @return Core_Model_NetworkResource
     */        
    protected function _findResource($name) {
        $resource = $this->_repository->findByName($name);
        if(empty($resource)) {
            throw new Core_Service_Exception("No existe el resource $name");
        }
        
        return $resource;
    }

    protected function _existsName($name) {
        $object = $this->_repository->findByName($name);
        if(!empty($object)) {
            return true;
        }
        
        return false;
    }

    /**
     * 
     * @param string $name
     * @return int
     */
    protected function _removeResource($name) {
        $resource = $this->_findResource($name);
        $profiles = $this->_profileFinder->findByNetworkResource($resource);
        if(!empty($profiles)) {
            $profileName = $profiles[0]->getName();
            throw new Core_Service_Exception("El recurso se está usando en el perfil: $profileName");
        }
        
        $this->_repository->remove($resource);
        Core_Model_AuditHelper::log("Eliminar recurso" , $name);        
    }
    
    
    /**
     * 
     * @param array $args
     * @return array
     */
    public function createResource($args) {
        if(!isset($args['name']) || !isset($args['type'])) {
            throw new Core_Service_Exception("Falta argumento requerido");
        }
        
        if(!isset($args['desc'])) {
            $args['desc'] = "";
        }
        
        switch($args['type']) {
            case 'host':
                if(!isset($args['ipaddr'])) {
                    throw new Core_Service_Exception("Falta argumento requerido");
                }
                
                $resource = $this->_createHost(
                                $args['name'], 
                                $args['ipaddr'], 
                                $args['desc']
                            );
                break;
            case 'subnet':
                if(!isset($args['netaddr']) || !isset($args['netmask'])) {
                    throw new Core_Service_Exception("Falta argumento requerido");
                }
                
                $resource = $this->_createSubnet(
                                $args['name'],
                                $args['netaddr'], $args['netmask'], 
                                $args['desc']
                            );                
                break;
            case 'range':
                if(!isset($args['beginip']) || !isset($args['endip'])) {
                    throw new Core_Service_Exception("Falta argumento requerido");
                }
                
                $resource = $this->_createRange(
                                $args['name'],
                                $args['beginip'], $args['endip'], 
                                $args['desc']
                            );
                break;
        }
        
        return $resource->toArray();
    }
    
    /**
     * 
     * @param array $args
     * @return array
     */
    public function modifyResource($args) {
        if(!isset($args['name'])) {
            throw new Core_Service_Exception("Falta argumento requerido");
        }
        
        $resource = $this->_findResource($args['name']);
        switch($resource->getType()) {
            case 'host':
                if(isset($args['ipaddr'])) {
                    $resource->setIp(new Core_Model_IPAddress($args['ipaddr']));
                }
                break;
            case 'subnet':
                if(isset($args['netaddr']) && isset($args['netmask'])) {
                    $netObj = new Core_Model_NetworkAddress(
                                new Core_Model_IPAddress($args['netaddr']), 
                                new Core_Model_IPAddress($args['netmask'])
                            );
                    $resource->setSubnet($netObj);
                }
                break;
            case 'range':
                if(isset($args['beginip'])) {
                    $resource->setBeginIp(new Core_Model_IPAddress($args['beginip']));
                }
                if(isset($args['endip'])) {
                    $resource->setEndIp(new Core_Model_IPAddress($args['endip']));
                }
                break;
        }
        
        if(isset($args['desc'])){
            $resource->setDesc($args['desc']);
        }

        Core_Model_AuditHelper::updateInfo($resource);
        $this->_repository->persist($resource);
        Core_Model_AuditHelper::log("Modificado recurso" , $resource->toArray());

        return $resource->toArray();
    }
    
    /**
     * 
     * @param array $args
     * @return int
     */
    public function removeResource($args) {
        if(!isset($args['name'])) {
            throw new Core_Service_Exception("Falta argumento");
        }
        
        $this->_removeResource($args['name']);
    }
    
    /**
     * 
     * @param array $args
     * @return array
     */
    public function showResource($args) {
        if(!isset($args['name'])) {
            throw new Core_Service_Exception("Falta argumento");
        }
        $resource = $this->_findResource($args['name']);

        return $resource->toArray();
    }
    
    /**
     * 
     * @return array[]
     */
    public function listResources() {
        $resourceList = array();
        $resources = $this->_repository->findAll();
        foreach($resources as $resource) {
            $resourceList[] = $resource->toArray();
        }
        
        return $resourceList;
    }

}
