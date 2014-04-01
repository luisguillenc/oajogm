<?php

/**
 * Clase que implementa el repositorio de recursos de red usando una 
 * base de datos relacional
 * 
 * @category OAJogm
 * @package Core
 * @subpackage Models
 * @author luis
 */
class Core_Model_NetworkResourceDbRepository 
    extends LGC_Model_DbRepository
    implements Core_Model_NetworkResourceRepositoryInterface
{
    public function __construct() {
        $dbTable =  new Core_Model_DbTable_NetworkResource();
        parent::__construct(
                "Core_Model_NetworkResource", $dbTable
                );
        
        $this->_setDbTable("Core_Model_HostNetworkResource", $dbTable);
        $this->_setDbTable("Core_Model_SubnetNetworkResource", $dbTable);
        $this->_setDbTable("Core_Model_RangeNetworkResource", $dbTable);
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
        $data['type'] = $object->getType();
        switch($object->getType()) {
            case 'host':
                $data['ip'] = $object->getIp()->__toString();
                break;
            case 'subnet':
                $data['network_addr'] = $object->getSubnet()
                                            ->getNetAddress()->__toString();
                $data['network_mask'] = $object->getSubnet()
                                            ->getNetMask()->__toString();
                break;
            case 'range':
                $data['begin_ip'] = $object->getBeginIp()->__toString();
                $data['end_ip'] = $object->getEndIp()->__toString();
                break;
            default:
                throw new Core_Model_Exception("Tipo desconocido");
        }
        
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
     * @return Core_Model_NetworkResource
     * @throws Core_Model_Exception si no logra hidratar el objeto
     */
    protected function _hydrateObject($data) {
        switch($data['type']) {
            case 'host':
                $object = new Core_Model_HostNetworkResource(
                        $data['name'], 
                        new Core_Model_IPAddress($data['ip']), 
                        $data['desc']
                        );
                break;
            case 'subnet':
                $object = new Core_Model_SubnetNetworkResource(
                        $data['name'],
                        new Core_Model_NetworkAddress(
                                new Core_Model_IPAddress($data['network_addr']),
                                new Core_Model_IPAddress($data['network_mask'])
                                ),
                        $data['desc']
                        );
                break;
            case 'range':
                $object = new Core_Model_RangeNetworkResource(
                        $data['name'],
                        new Core_Model_IPAddress($data['begin_ip']),
                        new Core_Model_IPAddress($data['end_ip']),
                        $data['desc']
                        );
                break;
        }
        
        $this->_setObjectId($object, $data['id']);

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
     * @param type $name
     * @return null|Core_Model_NetworkResource
     */
    public function findByName($name) {
        $resources = $this->_findByField('name', $name);
        if(empty($resources)) {
            return null;
        }
        return $resources[0];
        
    }
    
    /**
     * @param string $type
     * @return Core_Model_NetworkResource[]
     */
    public function findByResourceType($type) {
        return $this->_findByField('type', $type);
    }
    
}