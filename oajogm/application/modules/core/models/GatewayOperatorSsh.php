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
    extends Core_Model_GatewayOperatorLocal
    implements Core_Model_GatewayOperatorInterface
{

    /**
     * Array con las opciones de conexión remota
     *  ['host']
     *  ['port']
     *  ['username']
     *  ['password']
     *  ['rsa_key']
     *  ['rsa_key_pub']
     * @var array
     */
    protected $_sshOptions = array();

    protected $_connection = null;

    protected static $_pathKeys = "";
    
    
    public static function setPathSshKeys($pathKeys) {
        self::$_pathKeys = $pathKeys;
    }

    
    /**
     * @param array $sshOptions
     */
    public function __construct($sshOptions) {
        if(empty($sshOptions)) {
            throw new Core_Model_Exception("Faltan opciones ssh");
        }
        if(!array_key_exists('host', $sshOptions)) {
            throw new Core_Model_Exception("Faltan opción ssh: host");
        }
        if(!array_key_exists('port', $sshOptions)) {
            throw new Core_Model_Exception("Faltan opción ssh: port");
        }
        if(!array_key_exists('username', $sshOptions)) {
            throw new Core_Model_Exception("Faltan opción ssh: username");
        }
        if(!array_key_exists('password', $sshOptions)) {
            $sshOptions['password'] = "";
        }
        if(!array_key_exists('rsa_key', $sshOptions)) {
            throw new Core_Model_Exception("Faltan opción ssh: rsa_key");
        }
        if(!array_key_exists('rsa_key_pub', $sshOptions)) {
            $sshOptions['rsa_key_pub'] = $sshOptions['rsa_key'].".pub";
        }
        
        $this->_sshOptions = $sshOptions;
    }
    
    
    protected function _getConnection() {
        if($this->_connection == null) {
            $this->_connection = ssh2_connect(
                        $this->_sshOptions['host'], 
                        $this->_sshOptions['port'], 
                        array('hostkey'=>'ssh-rsa')
                    );

            $connSuccess = ssh2_auth_pubkey_file(
                                $this->_connection, 
                                $this->_sshOptions['username'], 
                                self::$_pathKeys."/".$this->_sshOptions['rsa_key_pub'], 
                                self::$_pathKeys."/".$this->_sshOptions['rsa_key'], 
                                $this->_sshOptions['password']
                    );
            
            if(!$connSuccess) {
                $this->_connection = null;
            }
        }
        
        return $this->_connection;
    }

    protected function _execCommand($cmdExec, &$output, &$exitStatus) {
        $connection = $this->_getConnection();
        if($connection === null) {
            throw new Core_Model_Exception("Error al obtener la conexión ssh!");
        }
        
        $stream = ssh2_exec($connection, $cmdExec);

        if($stream === false) {
            throw new Core_Model_Exception("Error al obtener stream ssh!");
        }
        
        $errorStream = ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);

        stream_set_blocking( $stream, true );
        stream_set_blocking( $errorStream, true );

        while($line = fgets($stream)) {
            flush();
            $output[] = $line;
        }
        
        while($line = fgets($errorStream)) {
            flush();
            $output[] = $line;
        }
        
        $exitStatus = ssh2_get_exit_status($stream);
        
        fclose($errorStream);
        fclose($stream);
    }

}

