<?php


/**
 * Clase que implementa la factoría con la que se deberán crear los clientes 
 * de acceso. Esta factoría tendrá la responsabilidad de garantizar que 
 * el nombre del cliente de acceso no se encuentra en el repositorio.
 * 
 * @category OAJogm
 * @package Core
 * @subpackage Models
 * @author luis
 */
class Core_Model_AccessClientFactory {
    
    /** @var Core_Model_AccessClientFinderInterface  */
    protected $_finder;
    
    /**
     * @param Core_Model_AccessClientFinderInterface $finder
     */
    public function __construct(
            Core_Model_AccessClientFinderInterface $finder
            ) {
        $this->_finder = $finder;
    }
    
    /**
     * Método de creación de clientes de acceso
     * 
     * @param string $name
     * @param Core_Model_IPAddress $vpnIp dirección IP reservada de la VPN
     * @param Core_Model_AccessProfile $profile perfil de acceso asignado
     * @return Core_Model_AccessClient
     * @throws Core_Model_Exception si existe el cliente con el nombre
     */
    public function createAccessClient(
            $name, 
            Core_Model_IPAddress $vpnIp, 
            Core_Model_AccessProfile $profile
            ) {
        $result = $this->_finder->findByName($name);
        if(!empty($result)) {
            throw new Core_Model_Exception("Ya existe cliente con ese nombre");
        }
        
        return new Core_Model_AccessClient($name, $vpnIp, $profile);
    }

}
