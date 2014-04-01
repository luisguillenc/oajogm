<?php


/**
 * Interfaz que define los métodos disponibles para encontrar perfiles de 
 * acceso
 * 
 * @category OAJogm
 * @package Core
 * @subpackage Models
 * @author luis
 */
interface Core_Model_AccessProfileFinderInterface
{
    /**
     * @param int $id
     * @return Core_Model_AccessProfile
     */
    public function find($id);

    /**
     * @param string $name
     * @return Core_Model_AccessProfile
     */
    public function findByName($name);

    /**
     * @return Core_Model_AccessProfile[]
     */
    public function findAll();
    
    /**
     * Retorna los perfiles de acceso que contienen al recurso pasado
     * 
     * @param Core_Model_NetworkResource $resource
     * @return Core_Model_AccessProfile[]
     */
    public function findByNetworkResource(Core_Model_NetworkResource $resource);
}

