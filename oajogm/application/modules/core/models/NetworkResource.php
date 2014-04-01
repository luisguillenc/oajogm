<?php


/**
 * Clase abstracta que almacena la información general de los recursos de red
 * 
 * @category OAJogm
 * @package Core
 * @subpackage Models
 * @author luis
 */
abstract class Core_Model_NetworkResource 
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
     * 
     *  @var string 
     */
    protected $_desc;
    
    /**
     * @param string $name
     * @param string $desc
     */
    protected function __construct($name, $desc = "") {

        if(!preg_match('/^[a-z][a-z0-9_]{0,44}$/', $name)) {
            throw new Core_Model_Exception("Nombre no válido");
        }

        $this->_name = $name;
        $this->_desc = $desc;
    }
    
    /**
     * @return int
     */
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
     * Método abstracto que devolverá un string identificativo del tipo de 
     * recurso. Cada tipo de recurso tendrá un identificador único.
     * 
     * @return string 
     */
    abstract public function getType();

    
    /** @param Core_Model_AuditInfo $auditInfo */
    public function setAuditInfo(Core_Model_AuditInfo $auditInfo) {
        $this->_auditInfo = $auditInfo;
    }
    
    /** @return Core_Model_AuditInfo */
    public function getAuditInfo() {
        return $this->_auditInfo;
    }
    
    
    /**
     * @return array
     */
    public function toArray() {
        $data = array();
        $data['id'] = $this->_id;
        $data['name'] = $this->_name;
        $data['desc'] = $this->_desc;
        $data['type'] = $this->getType();
        if($this->_auditInfo !== null) {
            $data['audit_info'] = $this->getAuditInfo()->toArray();
        }
        return $data;
    }

}


