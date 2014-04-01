<?php


/**
 * Implementación concreta de un logger de conexiones usando una tabla de 
 * base de datos
 * 
 * @category OAJogm
 * @package Core
 * @subpackage Models
 * @author luis
 */
class Core_Model_ConnectionLoggerDb
    implements Core_Model_ConnectionLoggerInterface
{
    
    /** @var Core_Model_DbTable_ConnectionLog */
    protected $_dbTable;
    
    public function __construct() {
        $this->_dbTable = new Core_Model_DbTable_ConnectionLog();
    }
    
    /**
     * @param string $name
     */
    public function connect($name) {
        $now = Zend_Date::now();
        $data = array();        
        $data['timestamp'] =  $now->getIso();
        $data['name'] = $name;
        $data['action'] = 'connect';
        
        $this->_dbTable->insert($data);
    }

    /**
     * @param string $name
     */
    public function disconnect($name) {
        $now = Zend_Date::now();
        $data = array();        
        $data['timestamp'] =  $now->getIso();
        $data['name'] = $name;
        $data['action'] = 'disconnect';
        
        $this->_dbTable->insert($data);
    }

    /**
     * @param int $number
     * @return array
     */
    public function getLastLogins($number = 10) {
        $select = $this->_dbTable->select()
                ->where("action = 'connect'");

        if($number > 0) {
            $selectCount = $this->_dbTable->select()
                    ->from($this->_dbTable, 'COUNT(*) as count')
                    ->where("action = 'connect'");
            $resultCount = $selectCount->query()->fetchAll();

            $total = $resultCount[0]['count'];


            $offset = $total - $number;
            if($offset > 0) {
                $select->limit($number, $offset);
            }            
        }
        
        return $select->query()->fetchAll();
    }

    /**
     * @param string $name
     * @param int $number
     */
    public function getUserHistory($name, $number = 0) {
        $select = $this->_dbTable->select()
                ->where("name = ?", $name);

        if($number >0) {
            $selectCount = $this->_dbTable->select()
                    ->from($this->_dbTable, 'COUNT(*) as count')
                    ->where("name = ?", $name);
            $resultCount = $selectCount->query()->fetchAll();

            $total = $resultCount[0]['count'];
            $offset = $total - $number;
            if($offset > 0) {
                $select->limit($number, $offset);
            }
        }

        return $select->query()->fetchAll();
    }
}
