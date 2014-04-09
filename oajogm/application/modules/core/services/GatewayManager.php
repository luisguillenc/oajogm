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
        try {
            return $this->_operatorGw->getConnectedClients();
        } catch (Core_Model_Exception $ex) {
            throw new Core_Service_Exception($ex->getMessage());
        }
    }
    
    /**
     * 
     * @param array $args
     */
    public function disconnectClient($args) {
        if(!isset($args['name'])) {
            throw new Core_Service_Exception("Falta argumento");
        }

        $name = $args['name'];
        
        $exists = false;
        $success = false;
        $connected = $this->_operatorGw->getConnectedClients();
        foreach($connected as $client) {
            if($client['cn'] == $name) {
                $exists = true;
                try {
                    $success = $this->_operatorGw->disconnectClient($name);
                } catch (Core_Model_Exception $ex) {
                    throw new Core_Service_Exception($ex->getMessage());
                }
            }
        }
        
        if(!$exists) {
            throw new Core_Service_Exception("El cliente no se encontraba conectado");
        }
        
        if($exists && !$success) {
            throw new Core_Service_Exception("No se recibió notificación de éxito");
        }
        
        Core_Model_AuditHelper::log("Desconectado cliente $name", $name);
    }
    
    /**
     * Habilita los accesos al gateway
     */
    public function enableAccess() {
        $this->_gateway->enableAccess();
        Core_Model_AuditHelper::log("Habilitado acceso al gateway");
    }
    
    /**
     * Deshabilita los accesos al gateway
     */
    public function disableAccess() {
        $this->_gateway->disableAccess();
        Core_Model_AuditHelper::log("Deshabilitado acceso al gateway");
    }
    
    /**
     * Recarga las reglas iptables en el gateway
     */
    public function reloadIptablesRules() {
        try {
            $status = $this->_operatorGw->reloadIptablesRules();
        } catch (Core_Model_Exception $ex) {
            throw new Core_Service_Exception($ex->getMessage());
        }

        if(!$status) {
            throw new Core_Service_Exception("Error cargando reglas iptables");
        }
        
        Core_Model_AuditHelper::log("Recargadas reglas iptables");
    }
    
    /**
     * Inicializa el servidor vpn del gateway
     */
    public function start() {
        try {
            $status = $this->_operatorGw->start();
        } catch (Core_Model_Exception $ex) {
            throw new Core_Service_Exception($ex->getMessage());
        }

        if(!$status) {
            throw new Core_Service_Exception("Error iniciando servidor");
        }
        Core_Model_AuditHelper::log("Iniciado servidor vpn");
    }
    
    /**
     * Detiene el servidor vpn del gateway
     */
    public function stop() {
        try {
            $status = $this->_operatorGw->stop();
        } catch (Core_Model_Exception $ex) {
            throw new Core_Service_Exception($ex->getMessage());
        }

        if(!$status) {
            throw new Core_Service_Exception("Error deteniendo servidor");
        }
        Core_Model_AuditHelper::log("Detenido servidor vpn");
    }
    
    /**
     * Devuelve el estado del gateway
     * @return array
     */
    public function status() {
        $status = array();
        $status['service_ip'] = $this->_gateway->getServiceIp()->__toString();
        $status['vpn_network'] = $this->_gateway->getVpnNetwork()->getNetAddress()->__toString();
        $status['routed_networks'] = array();
        foreach($this->_gateway->getRoutedNetworks() as $routedNet) {
            $status['routed_networks'][] = $routedNet->__toString();
        }
        $status['access_status'] = $this->_gateway->getAccessStatus();
        try {
            $status['vpn_service'] = $this->_operatorGw->isRunning();
        } catch (Core_Model_Exception $ex) {
            throw new Core_Service_Exception($ex->getMessage());
        }
        
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

    
    /**
     * 
     * @param array $args
     * @return string
     * @throws Core_Service_Exception
     */
    public function viewIptablesLog($args) {
        if(isset($args['number'])) {
            $number = $args['number'];
        } else {
            $number = 0;
        }

        try {
            $strLog = $this->_operatorGw->viewIptablesLog($number);
        } catch (Core_Model_Exception $ex) {
            throw new Core_Service_Exception($ex->getMessage());
        }
        
        return $strLog;
    }

}
