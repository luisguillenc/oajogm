<?php

/**
 * Clase que almacena la información de los perfiles de acceso
 * 
 * @category OAJogm
 * @package Core
 * @subpackage Models
 * @author luis
 */
class Core_Model_AccessProfile 
    implements LGC_Model_ObjectInterface, Core_Model_AuditableObjectInterface
{
    
    /** 
     * Identificador interno, si mayor que 0 el objeto estará en persistencia
     * el identificador es asignado por las clases del repositorio
     * 
     * @var int 
     */
    protected $_id;
    
    /** 
     * Información de auditoría del objeto
     * 
     * @var Core_Model_AuditInfo 
     */
    protected $_auditInfo = null;

    /** 
     * Nombre identificativo que deberá ser único. 
     * Deberá cumplir el formato definido en la especificación
     * 
     * @var string 
     */
    protected $_name;

    /** 
     * Descripción
     * @var string 
     */
    protected $_desc;
    
    /** 
     * Array asociativo que contiene las referencias a los recursos de red
     * a los que tiene acceso el perfil. El array se encuentra indexado 
     * por el nombre del recurso
     * 
     * @var Core_Model_NetworkResource[] 
     */
    protected $_resources;
    
    /**
     * 
     * @param string $name
     * @param string $desc ;optional
     */
    public function __construct($name, $desc = "") {
        
        if(!preg_match('/^[a-z][a-z0-9_]{0,44}$/', $name)) {
            throw new Core_Model_Exception("Nombre no válido");
        }

        $this->_resources = array();
        $this->_name = $name;
        $this->_desc = $desc;
    }

    /** @return int */
    public function getId() {
        return $this->_id;
    }
    
    /** @return string */
    public function getName() {
        return $this->_name;
    }

    /** @return string */
    public function getDesc() {
        return $this->_desc;
    }
    
    /** @param string $desc */
    public function setDesc($desc) {
        $this->_desc = $desc;
    }
    
    /**
     * Elimina todos los recursos asociados
     */
    public function cleanResources() {
        $this->_resources = array();
    }
    
    /**
     * @param string $nameRsrc
     * @return boolean
     */
    public function existsResource($nameRsrc) {
        return array_key_exists($nameRsrc, $this->_resources);
    }
    
    /**
     * Agrega el acceso al recurso pasado
     * 
     * @param Core_Model_NetworkResource $netRsrc
     * @throws Core_Model_Exception si existe el recurso o si se encuentra en persistencia
     */
    public function addResource(Core_Model_NetworkResource $netRsrc) {
        if(!$netRsrc->getId() > 0) {
            throw new Core_Model_Exception("El recurso debe estar en persistencia");
        }
        
        $nameRsrc = $netRsrc->getName();
        if($this->existsResource($nameRsrc)) {
            throw new Core_Model_Exception("Ya existe el recurso $netRsrc");
        }
        
        $this->_resources[$nameRsrc] = $netRsrc;
    }
    
    /**
     * Elimina el acceso al recurso padado
     * 
     * @param Core_Model_NetworkResource $netRsrc
     * @throws Core_Model_Exception si no existe el recurso
     */
    public function deleteResource(Core_Model_NetworkResource $netRsrc) {
        $nameRsrc = $netRsrc->getName();
        if(!$this->existsResource($nameRsrc)) {
            throw new Core_Model_Exception("No existe el recurso $netRsrc");
        }
        
        unset($this->_resources[$nameRsrc]);        
    }
    
    
    /** 
     * Obtiene un array con las referencias a los recursos a los que se tiene 
     * acceso
     * 
     * @return Core_Model_NetworkResource[] 
     */
    public function getResources() {
        $result = array();
        foreach($this->_resources as $resource) {
            $result[] = $resource;
        }
        
        return $result;
    }
    
    /**
     * Implementa el setter la interfaz auditable
     * 
     *  @param Core_Model_AuditInfo $auditInfo 
     */
    public function setAuditInfo(Core_Model_AuditInfo $auditInfo) {
        $this->_auditInfo = $auditInfo;
    }
    
    /** 
     * Implementa el getter de la iterfaz auditable
     * 
     * @return Core_Model_AuditInfo 
     */
    public function getAuditInfo() {
        return $this->_auditInfo;
    }
    
    /**
     * Retorna la información contenida en el objeto en formato array
     * 
     * @return array que contiene los datos del perfil
     */
    public function toArray() {
        $data = array();
        $data['id'] = $this->_id;
        $data['name'] = $this->_name;
        $data['desc'] = $this->_desc;
        if($this->_auditInfo !== null) {
            $data['audit_info'] = $this->getAuditInfo()->toArray();
        }
        
        $data['resources'] = array();
        foreach($this->_resources as $resource) {
            $data['resources'][] = $resource->toArray();
        }
        
        return $data;
    }

    
}
