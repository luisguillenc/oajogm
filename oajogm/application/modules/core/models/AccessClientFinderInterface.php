<?php

/**
 * Interfaz que define los métodos disponibles para encontrar clientes de 
 * acceso
 * 
 * @category OAJogm
 * @package Core
 * @subpackage Models
 * @author luis
 */
interface Core_Model_AccessClientFinderInterface
{
    /**
     * @param int $id
     * @return Core_Model_AccessClient
     */
    public function find($id);

    /**
     * @param string $name
     * @return Core_Model_AccessClient
     */
    public function findByName($name);

    /**
     * @return Core_Model_AccessClient[]
     */
    public function findAll();
    
    /**
     * Devuelve todos los clientes de acceso con el perfil pasado
     * 
     * @param Core_Model_AccessProfile $profile
     * @return Core_Model_AccessClient[]
     */
    public function findByProfile(Core_Model_AccessProfile $profile);
    
}

