<?php

/**
 * Clase que implementará la interfaz de operaciones sobre el gateway 
 * llamando al broker de control del gateway usando la librería libssh
 * 
 * @category OAJogm
 * @package Core
 * @subpackage Models
 * @author luis
 */
class Core_Model_GatewayOperatorSsh
    implements Core_Model_GatewayOperatorInterface
{

    /** 
     * Path al comando que implementa el broker de control en el servidor
     * remoto
     * 
     * @var string 
     */
    protected $_pathCommand;
    
    /**
     * Array con las opciones de conexión remota
     *  ['host']
     *  ['port']
     *  ['username']
     *  ['password']
     *  ['rsa_key']
     * @var array
     */
    protected $_sshOptions = array();
    
    /**
     * @param array $sshOptions
     * @param string $pathCommand
     */
    public function __construct($sshOptions, $pathCommand) {
        $this->_pathCommand = $pathCommand;
        $this->_sshOptions = $sshOptions;
    }
    
    /**
     * @param string $name
     */
    public function disconnectClient($name) {
        //exec path command option
    }

    /**
     *  @return array[]
     */
    public function getConnectedClients() {
        //exec path command option
        //obten salida
        //retorna string
    }

    /**
     * @return boolean
     */
    public function reloadIptablesRules() {
        //exec path command reloadiptables
    }

    /**
     * @return boolean
     */
    public function start() {
        //exec path command option
    }

    /**
     * @return boolean
     */
    public function stop() {
        //exec path command option
    }

    /**
     * @return boolean
     */
    public function isRunning() {
        //exec path command option
    }

}

