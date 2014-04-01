<?php

/**
 * Clase de servicio que proporciona una interfaz para la gestión de clientes
 * de acceso
 * 
 * @category OAJogm
 * @package Core
 * @subpackage Services
 * @author luis
 */
class Core_Service_AccessClientManager {
    
    /** @var Core_Model_AccessClientRepositoryInterface */
    protected $_repository;
    
    /** @var Core_Model_AccessClientFactory */
    protected $_factory;

    /** @var Core_Model_AccessProfileFinderInterface */
    protected $_profileFinder;
    
    /** @var Core_Model_VpnIpPoolerInterface */
    protected $_vpnIpPooler;
    
    /**
     * @param Core_Model_AccessClientRepositoryInterface $repository
     * @param Core_Model_AccessClientFactory $factory
     * @param Core_Model_AccessProfileFinderInterface $profileFinder
     * @param Core_Model_VpnIpPoolerInterface $vpnIpPooler
     */
    public function __construct(
            Core_Model_AccessClientRepositoryInterface $repository,
            Core_Model_AccessClientFactory $factory,
            Core_Model_AccessProfileFinderInterface $profileFinder,
            Core_Model_VpnIpPoolerInterface $vpnIpPooler
            ) {
        $this->_repository = $repository;
        $this->_factory = $factory;
        $this->_profileFinder = $profileFinder;
        $this->_vpnIpPooler = $vpnIpPooler;
    }
    
    /**
     * @param string $clientName
     * @param string $profileName
     * @param string $desc
     * @return Core_Model_AccessClient
     */
    protected function _createClient($clientName, $profileName, $desc = "") {
        if($this->_existsName($clientName)) {
            throw new Core_Service_Exception("Ya existe un cliente con ese nombre");
        }
        
        $profile = $this->_profileFinder->findByName($profileName);
        if(empty($profile)) {
            throw new Core_Service_Exception("No se encuentra el perfil $profileName");
        }
        
        $vpnIp = $this->_vpnIpPooler->lease();
        if($vpnIp === null) {
            throw new Core_Service_Exception("No hay direcciones libres en el pool");
        }
        
        $client = $this->_factory->createAccessClient($clientName, $vpnIp, $profile);
        $client->setDesc($desc);
        
        Core_Model_AuditHelper::createInfo($client);
        $this->_repository->persist($client);
        Core_Model_AuditHelper::log("Creado cliente" , $client->toArray());
        
        return $client;
    }
    
    /**
     * 
     * @param string $name
     * @return Core_Model_AccessClient
     */
    protected function _findClient($name) {
        $client = $this->_repository->findByName($name);
        if(empty($client)) {
            throw new Core_Service_Exception("No existe el cliente $name");
        }
        
        return $client;
    }

    protected function _existsName($name) {
        $object = $this->_repository->findByName($name);
        if(!empty($object)) {
            return true;
        }
        
        return false;
    }
    
    /**
     * @param string $name
     */
    protected function _removeClient($name) {
        $client = $this->_findClient($name);
        $vpnIp = $client->getVpnIp();
        
        $this->_repository->remove($client);
        $this->_vpnIpPooler->release($vpnIp);
        Core_Model_AuditHelper::log("Eliminado cliente" , $name);
    }
    
    /**
     * @param string $name
     * @return Core_Model_AccessClient
     */
    protected function _lockClient($name) {
        $client = $this->_findClient($name);
        $client->lock();
        
        Core_Model_AuditHelper::updateInfo($client);
        $this->_repository->persist($client);
        Core_Model_AuditHelper::log("Bloqueado cliente" , $name);
        
        return $client;
    }
    
    /**
     * @param string $name
     * @return Core_Model_AccessClient
     */
    protected function _unlockClient($name) {
        $client = $this->_findClient($name);
        $client->unlock();
        
        Core_Model_AuditHelper::updateInfo($client);
        $this->_repository->persist($client);
        Core_Model_AuditHelper::log("Desbloqueado cliente" , $name);
        
        return $client;
    }
    
    /**
     * @param string $name
     * @return Core_Model_AccessClient
     */
    protected function _enableIptablesLog($name) {
        $client = $this->_findClient($name);
        $client->enableIptablesLog();
        
        Core_Model_AuditHelper::updateInfo($client);
        $this->_repository->persist($client);
        Core_Model_AuditHelper::log("Habilitado log iptables de cliente", $name);
        
        return $client;
    }
    
    /**
     * @param string $name
     * @return Core_Model_AccessClient
     */
    protected function _disableIptablesLog($name) {
        $client = $this->_findClient($name);
        $client->disableIptablesLog();
        
        Core_Model_AuditHelper::updateInfo($client);
        $this->_repository->persist($client);
        Core_Model_AuditHelper::log("Deshabilitado log iptables de cliente", $name);
        
        return $client;
    }

    /**
     * @return array[]
     */
    public function listClients() {
        $clientList = array();
        $clients = $this->_repository->findAll();
        foreach($clients as $client) {
            $clientList[] = $client->toArray();
        }
        
        return $clientList;
    }

    /**
     * 
     * @param array $args
     * @return array
     */
    public function createClient($args) {
        if(!isset($args['name']) || !isset($args['prfname'])) {
            throw new Core_Service_Exception("Falta argumento");
        }
        
        if(!isset($args['desc'])) {
            $client = $this->_createClient($args['name'], $args['prfname']);
        } else {
            $client = $this->_createClient(
                    $args['name'], $args['prfname'], $args['desc']
                    );
        }
        
        return $client->toArray();
    }
    
    /**
     * 
     * @param array $args
     * @return array
     */
    public function modifyClient($args) {
        if(!isset($args['name'])) {
            throw new Core_Service_Exception("Falta argumento");
        }
        $client = $this->_findClient($args['name']);

        if(isset($args['desc'])) {
            $client->setDesc($args['desc']);
        }
        if(isset($args['prfname'])) {
            $profile = $this->_profileFinder->findByName($args['prfname']);
            if(empty($profile)) {
                throw new Core_Service_Exception("No se encuentra el perfil $profileName");
            }
            $client->setProfile($profile);
        }

        Core_Model_AuditHelper::updateInfo($client);
        $this->_repository->persist($client);
        Core_Model_AuditHelper::log("Cliente modificado", $client->toArray());
        
        return $client->toArray();
    }

    /**
     * 
     * @param array $args
     * @return array
     */
    public function showClient($args) {
        if(!isset($args['name'])) {
            throw new Core_Service_Exception("Falta argumento");
        }
        $client = $this->_findClient($args['name']);

        return $client->toArray();
    }
    
    /**
     * 
     * @param array $args
     */
    public function removeClient($args) {
        if(!isset($args['name'])) {
            throw new Core_Service_Exception("Falta argumento");
        }
        
        $this->_removeClient($args['name']);
    }

    
    /**
     * 
     * @param array $args
     * @return array
     */
    public function lockClient($args) {
        if(!isset($args['name'])) {
            throw new Core_Service_Exception("Falta argumento");
        }
        
        $client = $this->_lockClient($args['name']);
        return $client->toArray();
    }
    
    /**
     * 
     * @param array $args
     * @return array
     */
    public function unlockClient($args) {
        if(!isset($args['name'])) {
            throw new Core_Service_Exception("Falta argumento");
        }
        
        $client = $this->_unlockClient($args['name']);
        return $client->toArray();
    }
    
    /**
     * 
     * @param array $args
     * @return array
     */
    public function enableIptablesLog($args) {
        if(!isset($args['name'])) {
            throw new Core_Service_Exception("Falta argumento");
        }
        
        $client = $this->_enableIptablesLog($args['name']);
        return $client->toArray();
    }
    
    /**
     * 
     * @param array $args
     * @return array
     */
    public function disableIptablesLog($args) {
        if(!isset($args['name'])) {
            throw new Core_Service_Exception("Falta argumento");
        }
        
        $client = $this->_disableIptablesLog($args['name']);
        return $client->toArray();
    }

}
