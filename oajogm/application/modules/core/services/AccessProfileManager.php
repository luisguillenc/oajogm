<?php

/**
 * Clase de servicio que proporciona una interfaz para la gestión de perfiles
 * de acceso
 * 
 * @category OAJogm
 * @package Core
 * @subpackage Services
 * @author luis
 */
class Core_Service_AccessProfileManager {
    
    /** @var Core_Model_AccesProfileRepositoryInterface */
    protected $_repository;

    /** @var Core_Model_AccessProfileFactory */
    protected $_factory;

    /** @var Core_Model_NetworkResourceFinderInterface */
    protected $_resourceFinder;
    
    /** @var Core_Model_AccessClientFinderInterface */
    protected $_clientFinder;
    
    /**
     * 
     * @param Core_Model_AccessProfileRepositoryInterface $repository
     * @param Core_Model_AccessProfileFactory $factory
     * @param Core_Model_NetworkResourceFinderInterface $resourceFinder
     * @param Core_Model_AccessClientFinderInterface $clientFinder
     */
    public function __construct(
            Core_Model_AccessProfileRepositoryInterface $repository,
            Core_Model_AccessProfileFactory $factory,
            Core_Model_NetworkResourceFinderInterface $resourceFinder,
            Core_Model_AccessClientFinderInterface $clientFinder
            ) {
        $this->_repository = $repository;
        $this->_factory = $factory;
        $this->_resourceFinder = $resourceFinder;
        $this->_clientFinder = $clientFinder;
    }
    
    /**
     * 
     * @param string $name
     * @return Core_Model_NetworkResource
     */
    protected function _findResource($name) {
        $resource = $this->_resourceFinder->findByName($name);
        if(empty($resource)) {
            throw new Core_Service_Exception("No existe el resource $name");
        }
        
        return $resource;
    }

    /**
     * 
     * @param string $name
     * @return Core_Model_AccessProfile
     */
    protected function _findProfile($name) {
        $profile = $this->_repository->findByName($name);
        if(empty($profile)) {
            throw new Core_Service_Exception("No existe el perfil $name");
        }
        
        return $profile;
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
     * @param string $desc
     * @return Core_Model_AccessProfile
     */
    protected function _createProfile($name, $desc = "") {
        if($this->_existsName($name)) {
            throw new Core_Service_Exception("Ya existe un perfil con ese nombre");
        }

        $profile = $this->_factory->createAccessProfile($name);
        $profile->setDesc($desc);

        Core_Model_AuditHelper::createInfo($profile);
        $this->_repository->persist($profile);
        Core_Model_AuditHelper::log("Creado perfil" , $profile->toArray());
        
        return $profile;
    }
        
    /**
     * 
     * @param string $name
     */
    protected function _removeProfile($name) {
        $profile = $this->_findProfile($name);
        $clients = $this->_clientFinder->findByProfile($profile);
        if(!empty($clients)) {
            $clientName = $clients[0]->getName();
            throw new Core_Service_Exception("El cliente $clientName tiene asociado el perfil");
        }
        
        $this->_repository->remove($profile);
        Core_Model_AuditHelper::log("Eliminado perfil" , $name);        
    }
    
    /**
     * 
     * @param string $prfName
     * @param string $rscName
     * @return Core_Model_AccessProfile
     */
    protected function _addResource($prfName, $rscName) {
        $profile = $this->_findProfile($prfName);
        $resource = $this->_findResource($rscName);
        
        if($profile->existsResource($rscName)) {
            throw new Core_Service_Exception("Ya existe el recurso en el perfil");
        }
        $profile->addResource($resource);

        Core_Model_AuditHelper::updateInfo($profile);
        $this->_repository->persist($profile);
        Core_Model_AuditHelper::log("Agregado recurso" , "$rscName a $prfName");
        
        return $profile;
    }

    /**
     * 
     * @param string $prfName nombre del perfil
     * @param string $rscName nombre del recurso
     * @return Core_Model_AccessProfile
     */
    protected function _removeResource($prfName, $rscName) {
        $profile = $this->_findProfile($prfName);
        $resource = $this->_findResource($rscName);
        
        if(!$profile->existsResource($rscName)) {
            throw new Core_Service_Exception("No existe el recurso en el perfil");
        }
        $profile->deleteResource($resource);

        Core_Model_AuditHelper::updateInfo($profile);
        $this->_repository->persist($profile);
        Core_Model_AuditHelper::log("Eliminado recurso" , "$rscName de $prfName");
        
        return $profile;
    }


    

    /**
     * 
     * @param array $args
     * @return array
     */
    public function createProfile($args) {
        if(!isset($args['name'])) {
            throw new Core_Service_Exception("Falta argumento");
        }

        if(!isset($args['desc'])) {
            $profile =$this->_createProfile($args['name']);
        } else {
            $profile =$this->_createProfile($args['name'], $args['desc']);
        }
        

        return $profile->toArray();
    }

    /**
     * 
     * @param array $args
     * @return array
     */
    public function modifyProfile($args) {
        if(!isset($args['name'])) {
            throw new Core_Service_Exception("Falta argumento");
        }

        $profile = $this->_findProfile($args['name']);

        if(isset($args['desc'])) {
            $profile->setDesc($args['desc']);
            Core_Model_AuditHelper::updateInfo($profile);
            $this->_repository->persist($profile);
            Core_Model_AuditHelper::log("Modificado perfil" , $profile->toArray());
        }
        return $profile->toArray();
    }

    /**
     * 
     * @param array $args
     * @return int
     */
    public function removeProfile($args) {
        if(!isset($args['name'])) {
            throw new Core_Service_Exception("Falta argumento");
        }
        
        $this->_removeProfile($args['name']);
    }

    /**
     * 
     * @param array $args
     * @return array
     */
    public function addResource($args) {
        if(!isset($args['prfname']) || !isset($args['rscname'])) {
            throw new Core_Service_Exception("Falta argumento");
        }

        $profile = $this->_addResource($args['prfname'], $args['rscname']);
        return $profile->toArray();
    }
    
    /**
     * 
     * @param array $args
     * @return array
     */
    public function removeResource($args) {
        if(!isset($args['prfname']) || !isset($args['rscname'])) {
            throw new Core_Service_Exception("Falta argumento");
        }

        $profile = $this->_removeResource($args['prfname'], $args['rscname']);
        return $profile->toArray();
    }

    /**
     * 
     * @return array[]
     */
    public function listProfiles() {
        $profileList = array();
        $profiles = $this->_repository->findAll();
        foreach($profiles as $profile) {
            $profileList[] = $profile->toArray();
        }
        
        return $profileList;
    }
    
    /**
     * 
     * @param array $args
     * @return array
     */
    public function showProfile($args) {
        if(!isset($args['name'])) {
            throw new Core_Service_Exception("Falta argumento");
        }
        $profile = $this->_findProfile($args['name']);

        return $profile->toArray();
    }

}
