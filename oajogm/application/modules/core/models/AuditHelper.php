<?php

/**
 * Clase que contiene métodos con los que se realizarán las tareas de autitoría
 * 
 * @category OAJogm
 * @package Core
 * @subpackage Models
 * @author luis
 */
class Core_Model_AuditHelper 
{
    /** @var Core_Model_AuditLoggerInterface */
    static private $_logger;

    /**
     * Establece como logger de auditoría al objeto pasado
     * 
     * @param Core_Model_AuditLoggerInterface $logger
     */
    public static function setLogger(Core_Model_AuditLoggerInterface $logger) {
        self::$_logger = $logger;
    }
    
    /** 
     * Retornará el username del usuario loggeado en el sistema
     * 
     * @return string 
     */
    private static function getUsername() {
        $euid = posix_geteuid();
        $userData = posix_getpwuid($euid);
        if($userData['name'] == "www-data") {
            return Zend_Auth::getInstance()->getIdentity(); 
        } else {
            return $userData['name'];
        }
    }

    /**
     * Realiza el registro de auditoría de la acción pasada y los datos
     * 
     * @param string $action
     * @param mixed $data
     * @throws Core_Model_Exception si no se asignó logger
     */
    public static function log($action, $data = "") {
        if(self::$_logger === null) {
            throw new Core_Model_Exception("No se asignó logger de auditoría");
        }
        self::$_logger->log(Zend_Date::now(), self::getUsername(), $action, $data);
    }

    
    /**
     * 
     * @param int $number
     * @return array
     * @throws Core_Model_Exception
     */
    public static function getEvents($number = 0) {
        if(self::$_logger === null) {
            throw new Core_Model_Exception("No se asignó logger de auditoría");
        }
        return self::$_logger->getEvents($number);
    }
    
    /**
     * Crea la información de auditoría del objeto auditable
     * 
     * @param Core_Model_AuditableObjectInterface $object
     */
    public static function createInfo(Core_Model_AuditableObjectInterface $object)  {
        $username = self::getUsername();

        $auditInfo = new Core_Model_AuditInfo(
                Zend_Date::now(), $username, Zend_Date::now(), $username
                );
        
        $object->setAuditInfo($auditInfo);
    }
    
    /**
     * Actualiza la información de auditoría del objeto auditable
     * 
     * @param Core_Model_AuditableObjectInterface $object
     * @throws Core_Model_Exception si el objeto no tiene información de auditoría creada
     */
    public static function updateInfo(Core_Model_AuditableObjectInterface $object) {
        $info = $object->getAuditInfo();
        if(is_null($info)) {
            throw new Core_Model_Exception("No está disponible la información de auditoría");
        }
        
        $info->_update(Zend_Date::now(), self::getUsername());
    }
 
    /**
     * Carga en el objeto pasado la información de auditoría almacenada en 
     * el array pasado. Este método es últil para hidratar objetos.
     * 
     * $data['created'] -> Zend_Date
     * $data['created_by'] -> string
     * $data['updated'] -> Zend_Date
     * $data['updated_by'] -> string
     * 
     * @param Core_Model_AuditableObjectInterface $object
     * @param array $data
     */
    public static function loadInfo(Core_Model_AuditableObjectInterface $object, $data) {
        $auditInfo = new Core_Model_AuditInfo(
                $data['created'], $data['created_by'], 
                $data['updated'], $data['updated_by']
                );
        $object->setAuditInfo($auditInfo);
    }
}
