<?php


/**
 * Interfaz que especifica los métodos que debe implemetar los Loggers 
 * de auditoría
 * 
 * @category OAJogm
 * @package Core
 * @subpackage Models
 * @author luis
 */
interface Core_Model_AuditLoggerInterface {

    /**
     * @param Zend_Date $date
     * @param string $username
     * @param string $action
     * @param mixed $data
     */
    public function log(Zend_Date $date, $username, $action, $data = "");

    /**
     * Si 0 devuelve todo
     * Devuelve un array no indexado de la forma:
     *  ['timestamp']
     *  ['username']
     *  ['action']
     *  ['data']
     * @param int $number
     * @return array
     */
    public function getEvents($number);
}
