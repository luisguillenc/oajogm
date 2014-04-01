<?php


/**
 * Clase que almacena métodos con los que se realizarán las tareas de autitoría
 * 
 * @category OAJogm
 * @package Core
 * @subpackage Models
 * @author luis
 */
class Core_Model_AuditInfo {
    /** 
     * Contiene la fecha de creación
     * 
     * @var Zend_Date 
     */
    private $_created;

    /** 
     * Contiene el username del administrador que creó el objeto
     * @var string 
     */
    private $_createdBy;
    
    /** 
     * Contiene la fecha de última actualización
     * @var Zend_Date 
     */
    private $_updated;
    
    /** 
     * Contiene el username del administrador que actualizó el objeto
     * @var string 
     */
    private $_updatedBy;

    /**
     * 
     * @param Zend_Date $created
     * @param string $createdBy
     * @param Zend_Date $updated
     * @param string $updatedBy
     */
    public function __construct(
            Zend_Date $created, $createdBy, Zend_Date $updated, $updatedBy
            ) 
    {
        $this->_created = $created;
        $this->_createdBy = $createdBy;
        $this->_updated = $updated;
        $this->_updatedBy = $updatedBy;        
    }
    
    /**
     * @param Zend_Date $updated
     * @param string $updatedBy
     */
    public function _update(Zend_Date $updated, $updatedBy) {
        $this->_updated = $updated;
        $this->_updatedBy = $updatedBy;        
    }

    /** @return Zend_Date */
    public function getCreated() {
        return $this->_created;
    }
    
    /** @return string */
    public function getCreatedBy() {
        return $this->_createdBy;
    }

    /** @return Zend_Date */
    public function getUpdated() {
        return $this->_updated;
    }
        
    /** @return string */
    public function getUpdatedBy() {
        return $this->_updatedBy;
    }
    
    /**
     * @return array
     */
    public function toArray() {
        return array(
            'created' => $this->_created->get(Zend_Date::DATETIME),
            'created_by' => $this->_createdBy,
            'updated' => $this->_updated->get(Zend_Date::DATETIME),
            'updated_by' => $this->_updatedBy
        );
    }
    
}
