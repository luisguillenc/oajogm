<?php

/**
 * Clase que implementa el repositorio de clientes de acceso usando una 
 * base de datos relacional
 * 
 * @category OAJogm
 * @package Core
 * @subpackage Models
 * @author luis
 */
class Core_Model_AccessClientDbRepository 
    extends LGC_Model_DbRepository
    implements Core_Model_AccessClientRepositoryInterface
{
    
    /** 
     * Buscador de perfiles que usará para hidratar el objeto
     * 
     * @var Core_Model_AccessProfileFinderInterface 
     */
    protected $_profileFinder;
    
    /**
     * 
     * @param Core_Model_AccessProfileFinderInterface $finder
     */
    public function __construct(Core_Model_AccessProfileFinderInterface $finder) {
        $dbTable =  new Core_Model_DbTable_AccessClient();
        parent::__construct(
                "Core_Model_AccessClient", $dbTable
                );
        
        $this->_profileFinder = $finder;
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
        $data['vpn_ip'] = $object->getVpnIp()->__toString();
        $data['profile_id'] = $object->getProfile()->getId();
        $data['locked'] = $object->isLocked();
        $data['iptables_log'] = $object->isIptablesLogged();
        
        $auditInfo = $object->getAuditInfo();
        //mysql datetime 'YYYY-MM-dd HH:mm:ss'
        $data['created'] = $auditInfo->getCreated()->toString('YYYY-MM-dd HH:mm:ss');
        $data['created_by'] = $auditInfo->getCreatedBy();
        $data['updated'] = $auditInfo->getUpdated()->toString('YYYY-MM-dd HH:mm:ss');
        $data['updated_by'] = $auditInfo->getUpdatedBy();
        
        return $data;
    }
    
    /**
     * 
     * @param array $data
     * @return Core_Model_AccessClient
     * @throws Core_Model_Exception si no logra hidratar el objeto
     */
    protected function _hydrateObject($data) {
        $profile = $this->_profileFinder->find($data['profile_id']);
        if(empty($profile)) {
            throw new Core_Model_Exception("No existe profile con ese id");
        }
        
        $object = new Core_Model_AccessClient(
                $data['name'],
                new Core_Model_IPAddress($data['vpn_ip']),
                $profile,
                $data['desc']
                );
                
        $this->_setObjectId($object, $data['id']);

        if($data['locked']) {
            $object->lock();
        } else {
            $object->unlock();
        }
        if($data['iptables_log']) {
            $object->enableIptablesLog();
        } else {
            $object->disableIptablesLog();
        }
        
        //hago la conversión a Zend_Date
        $createdDate = new Zend_Date();
        $createdDate->set($data['created'], 'YYYY-MM-dd HH:mm:ss');
        $updatedDate = new Zend_Date();
        $updatedDate->set($data['updated'], 'YYYY-MM-dd HH:mm:ss');
        $data['created'] = $createdDate;
        $data['updated'] = $updatedDate;
        Core_Model_AuditHelper::loadInfo($object, $data);
        
        return $object;
    }    
    
    /**
     * @param string $name
     * @return null|Core_Model_AccessClient
     */
    public function findByName($name) {
        $clients = $this->_findByField('name', $name);
        if(empty($clients)) {
            return null;
        }
        return $clients[0];
    }
    
    /**
     * @param Core_Model_AccessProfile $profile
     * @return null|Core_Model_AccessClient[]
     */
    public function findByProfile(Core_Model_AccessProfile $profile) {
        return $this->_findByField('profile_id', $profile->getId());
    }
    
}