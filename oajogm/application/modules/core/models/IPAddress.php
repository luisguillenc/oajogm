<?php

/**
 * Clase que almacena la información de una dirección IPv4
 * 
 * @category OAJogm
 * @package Core
 * @subpackage Models
 * @author luis
 */
class Core_Model_IPAddress {

    /**
     * Almacenamiento interno de la dirección. Usará un array[0..3] para cada
     * número de la dirección.
     * 
     *  @var int[]
     */
    protected $_address;
    
    /**
     * Array [0..3] con la dirección ip
     * o string con la dirección ip
     * 
     * @param string|array $address
     * @throws Core_Model_Exception si no es válida
     */
    public function __construct($address) {
        if(!is_array($address)) {
            if(!filter_var($address, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                throw new Core_Model_Exception("Dirección ip no válida");
            }
            $address = explode(".", $address);            
        }
        
        if(count($address) != 4) {
            throw new Core_Model_Exception("Dirección ip no válida");
        }

        for($i=0;$i<4;$i++) {
            if(!settype($address[$i], "integer")) {
                throw new Core_Model_Exception("Dirección ip no válida");
            }
            if($address[$i]<0 || $address[$i]>255) {
                throw new Core_Model_Exception("Dirección ip no válida");
            }
        }

        $this->_address = $address;            
    }
    
    /**
     * Retorna array [0..3] con la dirección ip
     * @return int[]
     */
    public function getData() {
        return $this->_address;
    }
    
    /**
     * Retorna la representación de cadena de la dirección ip
     * 
     * @return string
     */
    public function __toString() {
        $str = $this->_address[0].".";
        $str.= $this->_address[1].".";
        $str.= $this->_address[2].".";
        $str.= $this->_address[3];

        return $str;
    }    
}
