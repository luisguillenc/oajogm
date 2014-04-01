<?php

/**
 * Interfaz que especifica los métodos que debe implementar un objeto 
 * con información de auditoría
 * 
 * @category OAJogm
 * @package Core
 * @subpackage Models
 * @author luis
 */
interface Core_Model_AuditableObjectInterface {
    /** 
     * Método getter con la información de auditoría
     * 
     * @return Core_Model_AuditInfo 
     */
    public function getAuditInfo();
    
    /** 
     * Método setter con la información de auditoría
     * 
     * @param Core_Model_AuditInfo $info 
     */
    public function setAuditInfo(Core_Model_AuditInfo $info);
}
