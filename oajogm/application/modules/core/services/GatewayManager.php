<?php

/**
 * Clase de servicio que proporciona una interfaz con la que se realizarán
 * operaciones sobre el gateway
 * 
 * @category OAJogm
 * @package Core
 * @subpackage Services
 * @author luis
 */
class Core_Service_GatewayManager {

    /** @var Core_Model_GatewayOperatorInterface */
    protected $_operatorGw;
    
    /** @var Core_Model_GatewayConfiguration */
    protected $_gateway;

    /** @var Core_Model_ConnectionLoggerInterface */
    protected $_cnLogger = null;

    /**
     * 
     * @param Core_Model_GatewayConfiguration $gateway
     * @param Core_Model_GatewayOperatorInterface $operatorGw
     * @param Core_Model_ConnectionLoggerInterface $cnLogger
     */
    public function __construct(
            Core_Model_GatewayConfiguration $gateway,
            Core_Model_GatewayOperatorInterface $operatorGw,
            Core_Model_ConnectionLoggerInterface $cnLogger
            ) {
        $this->_gateway = $gateway;
        $this->_operatorGw= $operatorGw;
        $this->_cnLogger = $cnLogger;
    }


    /**
     * 
     * @return array[]
     */
    public function showClients() {
        return $this->_operatorGw->getConnectedClients();
    }
    
    /**
     * 
     * @param array $args
     * @return boolean true si éxito
     */
    public function disconnectClient($args) {
        if(!isset($args['name'])) {
            throw new Core_Service_Exception("Falta argumento");
        }

        $name = $args['name'];
        
        Core_Model_AuditHelper::log("Desconectar cliente", $name);

        $success = false;
        $connected = $this->_operatorGw->getConnectedClients();
        foreach($connected as $client) {
            if($client['cn'] == $name) {
                $success = $this->_operatorGw->disconnectClient($name);
            }
        }

        return $success;
    }
    
    /**
     * Habilita los accesos al gateway
     */
    public function enableAccess() {
        Core_Model_AuditHelper::log("Habilitar acceso al gateway");
        $this->_gateway->enableAccess();
    }
    
    /**
     * Deshabilita los accesos al gateway
     */
    public function disableAccess() {
        Core_Model_AuditHelper::log("Deshabilitar acceso al gateway");
        $this->_gateway->disableAccess();
    }
    
    /**
     * Recarga las reglas iptables en el gateway
     * @return boolean true si éxito
     */
    public function reloadIptablesRules() {
        Core_Model_AuditHelper::log("Recargar reglas iptables");
        return $this->_operatorGw->reloadIptablesRules();
    }
    
    /**
     * Inicializa el servidor vpn del gateway
     * @return boolean true si éxito
     */
    public function start() {
        Core_Model_AuditHelper::log("Iniciar servidor vpn");
        return $this->_operatorGw->start();
    }
    
    /**
     * Detiene el servidor vpn del gateway
     * @return boolean true si éxito
     */
    public function stop() {
        Core_Model_AuditHelper::log("Detener servidor vpn");
        return $this->_operatorGw->stop();
    }
    
    /**
     * Devuelve el estado del gateway
     * @return array
     */
    public function status() {
        $status = array();
        $status['service_ip'] = $this->_gateway->getServiceIp();
        $status['access_status'] = $this->_gateway->getAccessStatus();
        $status['vpn_service'] = $this->_operatorGw->isRunning();
        
        return $status;
    }

    /**
     * @param array $args
     * @return array[]
     */
    public function listLastLogins($args) {
        
        if(isset($args['number'])) {
            return $this->_cnLogger->getLastLogins($args['number']);
        }
        
        return $this->_cnLogger->getLastLogins();
        
    }
    
    /**
     * @param array $args
     * @return array[]
     */
    public function listClientHistory($args) {
        if(!isset($args['name'])) {
            throw new Core_Service_Exception("Falta argumento");
        }

        if(isset($args['number'])) {
            return $this->_cnLogger->getUserHistory($args['name'], $args['number']);
        }
        
        return $this->_cnLogger->getUserHistory($args['name']);
    }

    /**
     * @param array $args
     * @return array[]
     */
    public function listAuditEvents($args) {
        if(isset($args['number'])) {
            return Core_Model_AuditHelper::getEvents($args['number']);
        }
        
        return Core_Model_AuditHelper::getEvents();
    }

}
