<?php

/**
 * Clase que implementará la interfaz de operaciones sobre el gateway 
 * llamando al broker de control del gateway usando un exec local
 * 
 * @category OAJogm
 * @package Core
 * @subpackage Models
 * @author luis
 */
class Core_Model_GatewayOperatorLocal 
    implements Core_Model_GatewayOperatorInterface
{

    const SUDO = "/usr/bin/sudo";
    const OVPNSERVICE = "oajog_ovpnservice.sh";
    const OVPNMGMT = "oajog_ovpnmgmt.sh";
    const IPTABLESCFG = "oajog_iptablescfg.sh";
    const IPTABLESLOG = "oajog_iptableslog.sh";
    
    /** @var string */
    protected static $_pathCommand = "";
    
    
    public static function setPathCommand($pathCommand) {
        self::$_pathCommand = $pathCommand;
    }

    /** 
     * Path al comando que implementa el broker de control en el servidor local
     * 
     */
    public function __construct() {

    }
    
    protected function _execCommand($cmdExec, &$output, &$exitStatus) {
        exec($cmdExec, $output, $exitStatus);
    }
    
    /**
     * @param string $name
     */
    public function disconnectClient($name) {
        $cmdExec = self::$_pathCommand."/".self::OVPNMGMT." disconnect $name";
        $output = array(); $exitStatus = 0;
        $this->_execCommand($cmdExec, $output, $exitStatus);
        if($exitStatus != 0) {
            throw new Core_Model_Exception("Error enviando desconexion");
        }
        foreach($output as $linea) {
            if(preg_match('/SUCCESS/', $linea)) {
                return true;
            }
        }

        return false;
    }

    /**
     *  @return array[]
     */
    public function getConnectedClients() {
        
        $cmdExec = self::$_pathCommand."/".self::OVPNMGMT." list";
        $output = array(); $exitStatus = 0;
        $this->_execCommand($cmdExec, $output, $exitStatus);
        if($exitStatus != 0) {
            throw new Core_Model_Exception("Error obteniendo clientes conectados");
        }
        $startList = false;
        $clients = array();
        foreach($output as $linea) {
            if(preg_match('/^>/', $linea)) {
                continue;
            }
            if(preg_match('/^ROUTING TABLE/', $linea)) {
                break;
            }
            if(preg_match('/^Common Name,Real Address/', $linea)) {
                $startList = true;
            } else {
                if($startList) {
                    $datos = explode(",", $linea);
                    $clients[] = array(
                        'cn' => $datos[0],
                        'real_address' => $datos[1],
                        'bytes_received' => $datos[2],
                        'bytes_sent' => $datos[3],
                        'connected_since' => $datos[4]
                    );
                }
            }
        }
        return $clients;
    }

    /**
     * @return boolean
     */
    public function reloadIptablesRules() {
        $cmdExec = self::SUDO." ".self::$_pathCommand."/".self::IPTABLESCFG." reload";
        $output = array(); $exitStatus = 0;
        $this->_execCommand($cmdExec, $output, $exitStatus);
        if($exitStatus != 0) {
            return false;
        }
        
        return true;
    }

    public function viewIptablesLog($number = 0) {
        if(is_numeric(!$number)) {
            //para evitar inyección de código
            throw new Core_Model_Exception("Argumento inválido");
        }
        
        if($number > 0) {
            $cmdExec = self::$_pathCommand."/".self::IPTABLESLOG." $number";
        } else {
            $cmdExec = self::$_pathCommand."/".self::IPTABLESLOG;
        }

        $output = array(); $exitStatus = 0;
        $this->_execCommand($cmdExec, $output, $exitStatus);
        if($exitStatus != 0) {
            throw new Core_Model_Exception("Error obteniendo log iptables");
        }
        
        $retStr = "";
        foreach($output as $line) {
            $retStr.="$line\n";
        }
        
        return $retStr;
    }
    
    /**
     * @return boolean
     */
    public function start() {
        $cmdExec = self::SUDO." ".self::$_pathCommand."/".self::OVPNSERVICE." start";
        $output = array(); $exitStatus = 0;
        $this->_execCommand($cmdExec, $output, $exitStatus);
        if($exitStatus != 0) {
            return false;
        }
        
        return true;
    }

    /**
     * @return boolean
     */
    public function stop() {
        $cmdExec = self::SUDO." ".self::$_pathCommand."/".self::OVPNSERVICE." stop";
        $output = array(); $exitStatus = 0;
        $this->_execCommand($cmdExec, $output, $exitStatus);
        if($exitStatus != 0) {
            return false;
        }
        
        return true;
    }

    /**
     * @return boolean
     */
    public function isRunning() {
        $cmdExec = self::SUDO." ".self::$_pathCommand."/".self::OVPNSERVICE." status";
        $output = array(); $exitStatus = 0;
        $this->_execCommand($cmdExec, $output, $exitStatus);
        if($exitStatus != 0) {
            return false;
        }
        
        return true;
    }
}

