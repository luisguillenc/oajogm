<?php

/**
 * Interfaz que define los métodos que deberá implementar un logger de 
 * auditoría
 * 
 * @category OAJogm
 * @package Core
 * @subpackage Models
 * @author luis
 */
interface Core_Model_ConnectionLoggerInterface {

    /**
     * Loggerá una conexión del nombre del cliente de acceso
     * 
     * @param string $name
     */
    public function connect($name);

    /**
     * Loggerá una desconexión del nombre del cliente de acceso
     * 
     * @param string $name
     */
    public function disconnect($name);
    
    /**
     * Obtendrá en un array los datos con la información de los últimos
     * loggins realizados
     * 
     * @param int $number
     * @return array
     */
    public function getLastLogins($number);

    /**
     * Obtendrá en un array los datos con la información de los últimos
     * loggins y desconexiones realizadas del usuario pasado
     * 
     * @param string $name
     * @param int $number
     */
    public function getUserHistory($name, $number);

}
