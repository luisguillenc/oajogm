<?php

/**
 * Interfaz que define los métodos disponibles para encontrar recursos de 
 * red
 * 
 * @category OAJogm
 * @package Core
 * @subpackage Models
 * @author luis
 */
interface Core_Model_NetworkResourceFinderInterface
{
    /**
     * @param int $id
     * @return Core_Model_NetworkResource
     */
    public function find($id);

    /**
     * @param string $name
     * @return Core_Model_NetworkResource
     */
    public function findByName($name);

    
    /**
     * Retorna los recursos de red del tipo pasado. El string del tipo debe
     * coincidir con el identificador asignado en la clase.
     * 
     * @param string $type
     * @return Core_Model_NetworkResource
     */
    public function findByResourceType($type);


    /**
     * @return Core_Model_NetworkResource[]
     */
    public function findAll();
}

