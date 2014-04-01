<?php


/**
 * Clase que implementa la factoría con la que se deberán crear los perfiles 
 * de acceso. Esta factoría tendrá la responsabilidad de garantizar que 
 * el nombre del perfil de acceso no se encuentra en el repositorio.
 * 
 * @category OAJogm
 * @package Core
 * @subpackage Models
 * @author luis
 */
class Core_Model_AccessProfileFactory {
    
    /** @var Core_Model_AccessProfileFinderInterface  */
    protected $_finder;
    
    
    /**
     * @param Core_Model_AccessProfileFinderInterface $finder
     */
    public function __construct(
            Core_Model_AccessProfileFinderInterface $finder
            ) {
        $this->_finder = $finder;
    }
    
    /**
     * @param string $name
     * @return Core_Model_AccessProfile
     * @throws Core_Model_Exception si existe ya el nombre
     */
    public function createAccessProfile($name) {
        $result = $this->_finder->findByName($name);
        if(!empty($result)) {
            throw new Core_Model_Exception("Ya existe perfil con ese nombre");
        }

        return new Core_Model_AccessProfile($name);
    }

    
}
