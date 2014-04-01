<?php

/**
 * Clase que almacena la información de los clientes de acceso
 * 
 * @category OAJogm
 * @package Core
 * @subpackage Models
 * @author luis
 */
class Core_Model_AccessClient 
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
     * Perfil de acceso asociado al cliente
     * 
     * @var Core_Model_AccessProfile 
     */
    protected $_profile;
    
    /** 
     * Dirección IP de la VPN asignado al cliente
     * 
     * @var Core_Model_IPAddress 
     */
    protected $_vpnIp;
    
    /** 
     * Descripción
     * 
     * @var string 
     */
    protected $_desc;

    /** 
     * Almacena el estado de bloqueo del cliente
     * 
     * @var boolean 
     */
    protected $_locked;
    
    /** 
     * Almacena si se debe de hacer loggeo iptables del cliente
     * 
     * @var boolean 
     */
    protected $_iptablesLog;
    
    /**
     * 
     * @param type $name Nombre identificativo
     * @param Core_Model_IPAddress $vpnIp
     * @param Core_Model_AccessProfile $profile
     * @param type $desc
     */
    public function __construct(
            $name, 
            Core_Model_IPAddress $vpnIp,
            Core_Model_AccessProfile $profile, 
            $desc = "") {

        if(!preg_match('/^[a-z][a-z0-9_]{0,44}$/', $name)) {
            throw new Core_Model_Exception("Nombre no válido");
        }
        $this->_name = $name;
        $this->_profile = $profile;
        $this->_vpnIp = $vpnIp;
        $this->_desc = $desc;
        $this->_locked = true;
        $this->_iptablesLog = false;
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
     * Retorna la dirección IP asignada
     * @return Core_Model_IPAddress 
     */
    public function getVpnIp() {
        return $this->_vpnIp;
    }
    
    /** 
     * Establece el perfil de acceso del cliente
     * 
     * @param Core_Model_AccessProfile $profile
     * @throws Core_Model_Exception si el perfil pasado no se encuentra almacenado en persistencia
     */
    public function setProfile(Core_Model_AccessProfile $profile) {
        if(!$profile->getId() > 0) {
            throw new Core_Model_Exception("El perfil debe estar en persistencia");
        }        

        $this->_profile = $profile;
    }
    
    /** 
     * Retorna el perfil al que pertenece el cliente
     * 
     * @return Core_Model_AccessProfile 
     */
    public function getProfile() {
        return $this->_profile;
    }
    
    /**
     * Marca el cliente como bloqueado
     */
    public function lock() {
        $this->_locked = true;
    }

    /**
     * Desbloquea al cliente
     */
    public function unlock() {
        $this->_locked = false;
    }
    
    /** 
     * Nos indica si está bloqueado
     * 
     * @return boolean 
     */
    public function isLocked() {
        return $this->_locked;
    }
    
    /**
     * Habilita el logging de iptables para el cliente
     */
    public function enableIptablesLog() {
        $this->_iptablesLog = true;
    }
    
    /**
     * Deshabilita el logging de iptables
     */
    public function disableIptablesLog() {
        $this->_iptablesLog = false;
    }
    
    /** 
     * Devuelve cierto si está habilitado el logging de iptables para el cliente
     * 
     * @return boolean 
     */
    public function isIptablesLogged() {
        return $this->_iptablesLog;
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
        $data['ipaddr'] = $this->_vpnIp->__toString();
        $data['profile'] = $this->_profile->getName();
        $data['locked'] = $this->isLocked();
        $data['iptableslogged'] = $this->isIptablesLogged();
        
        return $data;
    }

    
}

