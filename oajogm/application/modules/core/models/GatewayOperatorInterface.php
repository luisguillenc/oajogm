<?php

/**
 * Interfaz que deberá implementar las clases que realizarán operaciones
 * sobre el gateway
 * 
 * @category OAJogm
 * @package Core
 * @subpackage Models
 * @author luis
 */
interface Core_Model_GatewayOperatorInterface {

    /**
     * Consultará al gateway información sobre los clientes conectados 
     * en el servidor vpn
     * 
     *  @return array[]
     */
    public function getConnectedClients();
    
    /** 
     * Mandará al gateway la orden de desconexión del cliente con el nombre 
     * identificativo pasado
     * 
     * @param string $name 
     */
    public function disconnectClient($name);
    
    /**
     * Enviará al gateway la orden de recargar las reglas iptables desde el manager
     * 
     * @return boolean si éxito
     */
    public function reloadIptablesRules();
    
    /**
     * Enviará al gateway la orden de iniciar el servidor vpn
     * 
     * @return boolean true si éxito
     */
    public function start();
    
    /**
     * Enviará al gateway la orden de detener el servidor vpn
     * 
     * @return boolean true siéxito
     */
    public function stop();
    
    /**
     * Consultará al gateway si el servidor vpn está en ejecución
     * 
     * @return boolean
     */
    public function isRunning();
    
    /**
     * Retorna las últimas líneas del log iptables
     * 
     * @return string
     */
    public function viewIptablesLog($number);
    
}

