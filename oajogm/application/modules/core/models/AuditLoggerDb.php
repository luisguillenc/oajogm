<?php


/**
 * Clase implementa un logger de auditoría usando una tabla de base de datos
 * 
 * @category OAJogm
 * @package Core
 * @subpackage Models
 * @author luis
 */
class Core_Model_AuditLoggerDb
    implements Core_Model_AuditLoggerInterface
{
    
    /** @var Core_Model_DbTable_AuditLog */
    protected $_dbTable;
    
    public function __construct() {
        $this->_dbTable = new Core_Model_DbTable_AuditLog();
    }

    /**
     * 
     * @param int $number
     * @return array
     */
    public function getEvents($number = 0) {
        $select = $this->_dbTable->select();
        
        if($number >0) {
            $selectCount = $this->_dbTable->select()
                    ->from($this->_dbTable, 'COUNT(*) as count');
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
     * @param Zend_Date $date
     * @param string $username
     * @param string $action
     * @param mixed $data
     */
    public function log(Zend_Date $date, $username, $action, $data = "") {
        $dataLog = array();
        $dataLog['timestamp'] =  $date->getIso();
        $dataLog['username'] = $username;
        $dataLog['action'] = $action;
        if(is_array($data) || is_object($data)) {
            $dataLog['data'] = var_export($data, true);
        } else {
            $dataLog['data'] = $data;
        }        
        
        $this->_dbTable->insert($dataLog);
    }
}
