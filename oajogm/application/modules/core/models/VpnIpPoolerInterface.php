<?php

/**
 * Interfaz que deberá implementar las clases que se encarguen de la gestión
 * del pool de direciones de la VPN
 * 
 * @category OAJogm
 * @package Core
 * @subpackage Models
 * @author luis
 */
interface Core_Model_VpnIpPoolerInterface {

    /**
     * Liberará del pool la dirección IP pasada
     * 
     * @param Core_Model_IPAddress $ip 
     */
    public function release(Core_Model_IPAddress $ip);
    
    /** 
     * Tomará una dirección IP del pool y la marcará como usada
     * 
     * @return Core_Model_IPAddress 
     */
    public function lease();
    
}
