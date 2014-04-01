<?php

/**
 * Clase que implementa el repositorio de perfiles de acceso usando una 
 * base de datos relacional
 * 
 * @category OAJogm
 * @package Core
 * @subpackage Models
 * @author luis
 */
class Core_Model_AccessProfileDbRepository 
    extends LGC_Model_DbRepository
    implements Core_Model_AccessProfileRepositoryInterface
{    
    /** 
     * Buscador de recursos de red necesario para la hidratación del objeto
     * 
     * @var Core_Model_NetworkResourceFinderInterface 
     */
    protected $_resourceFinder;
    
    /**
     * @param Core_Model_NetworkResourceFinderInterface $resourceFinder
     */
    public function __construct(
             Core_Model_NetworkResourceFinderInterface $resourceFinder
            ) 
    {

        parent::__construct(
                "Core_Model_AccessProfile", 
                new Core_Model_DbTable_AccessProfile()
                );
        
        $this->_setDbTable(
                "AccessProfile_Resources", 
                new Core_Model_DbTable_ProfileResource()
                );
        
        $this->_resourceFinder = $resourceFinder;
    }

    /**
     * @param int $profileId
     * @return int estado de la operación delete
     */
    protected function _cleanDbResources($profileId) {
        $where = $this->_getDbTable("AccessProfile_Resources")
                ->getAdapter()->quoteInto('profile_id = ?', $profileId);
        
        $ret = $this->_getDbTable("AccessProfile_Resources")->delete($where);
        return $ret;
    }
    
    
    /**
     * @param int $profileId
     * @return array con los datos de la interrelación
     */
    protected function _getDbResources($profileId) {
        $select = $this->_getDbTable("AccessProfile_Resources")
                ->select()->where("profile_id = ?", $profileId);
        
        return $this->_getDbTable("AccessProfile_Resources")->fetchAll($select);
    }
    

    /**
     * @param int $profileId
     * @param Core_Model_NetworkResource[]
     * @return int último id de recurso asignado
     */
    protected function _insertDbResources($profileId, $resources) {
        
        $lastId = 0;
        foreach($resources as $resource) {
            $data = array();
            
            $data['profile_id'] = $profileId;
            $data['resource_id'] = $resource->getId();

            $lastId = $this->_getDbTable("AccessProfile_Resources")->insert($data);
        }

        return $lastId;
    }
    
    /**
     * @param array $data
     * @return Core_Model_AccessProfile
     */
    protected function _hydrateObject($data) {
        
        $object = new Core_Model_AccessProfile($data['name'], $data['desc']);
        $this->_setObjectId($object, $data['id']);

        //hago la conversión a Zend_Date
        $createdDate = new Zend_Date();
        $createdDate->set($data['created'], 'YYYY-MM-dd HH:mm:ss');
        $updatedDate = new Zend_Date();
        $updatedDate->set($data['updated'], 'YYYY-MM-dd HH:mm:ss');
        $data['created'] = $createdDate;
        $data['updated'] = $updatedDate;
        Core_Model_AuditHelper::loadInfo($object, $data);
        
        
        $resourceList = $this->_getDbResources($object->getId());
        foreach($resourceList as $resourceEntry) {
            $resource = $this->_resourceFinder->find($resourceEntry['resource_id']);
            $object->addResource($resource);
        }
        
        return $object;
    }
    
    /**
     * @param LGC_Model_ObjectInterface $object
     * @return array
     */
    protected function _getDbData(LGC_Model_ObjectInterface $object) {
        $data = array();
        $data['id'] = $object->getId();
        $data['name'] = $object->getName();
        $data['desc'] = $object->getDesc();

        $auditInfo = $object->getAuditInfo();
        //mysql datetime 'YYYY-MM-dd HH:mm:ss'
        $data['created'] = $auditInfo->getCreated()->toString('YYYY-MM-dd HH:mm:ss');
        $data['created_by'] = $auditInfo->getCreatedBy();
        $data['updated'] = $auditInfo->getUpdated()->toString('YYYY-MM-dd HH:mm:ss');
        $data['updated_by'] = $auditInfo->getUpdatedBy();

        return $data;
    }
    
    /**
     * @param LGC_Model_ObjectInterface $object
     * @return int id objeto asignado
     */
    public function persist(LGC_Model_ObjectInterface $object) {
        
        if($object->getId() > 0) {
            $this->_cleanDbResources($object->getId());
        }
        
        $id = parent::persist($object);
        $this->_insertDbResources($object->getId(), $object->getResources());
        
        return $id;
    }
    
    /**
     * 
     * @param type $name
     * @return null|Core_Model_AccessProfile
     */
    public function findByName($name) {
        $data = $this->_findByField('name', $name);
        if(empty($data)) {
            return null;
        }
        return $data[0];
    }
    
    public function findByNetworkResource(Core_Model_NetworkResource $resource) {
        $select = $this->_getDbTable("AccessProfile_Resources")
                ->select()->where("resource_id = ?", $resource->getId());
        $rows = $this->_getDbTable("AccessProfile_Resources")->fetchAll($select);
        
        $data = array();
        foreach($rows as $row) {
            $data[] = $this->find($row['profile_id']);
        }
        
        return $data;
    }
}
