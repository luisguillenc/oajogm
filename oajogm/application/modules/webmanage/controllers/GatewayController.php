<?php

class Webmanage_GatewayController extends Zend_Controller_Action
{
    
    /** @var LGC_Controller_Action_Helper_LGCFlashMessenger */
    protected $_flashMessenger = null;

    /** @var Core_Service_AccessClientManager */
    protected $_clientMgr;

    /** @var Core_Service_ConnectionManager */
    protected $_connectionMgr;
    
    /** @var Core_Service_GatewayManager */
    protected $_gatewayMgr;
    
    public function init()
    {
        if(!Zend_Auth::getInstance()->hasIdentity()) {
             $this->_redirect('webmanage/login/index');
        }
        
        $this->_flashMessenger = $this->_helper->getHelper('LGCFlashMessenger');
        
        $sm = LGC_Service_Manager::getInstance();
        $this->_clientMgr = $sm->getService('access_client_manager');
        $this->_connectionMgr = $sm->getService('connection_manager');
        $this->_gatewayMgr = $sm->getService('gateway_manager');
    }
    
    public function indexAction()
    {
        $this->_forward("managestatus");
    }
    
    public function managestatusAction() {
        $this->view->actionTitle = "Gestionar estado";
        $this->view->status = $this->_gatewayMgr->status();
    }
    
    public function disconnectclientAction() {
        $name = $this->getRequest()->getParam('name');
        if(!$name) {
            throw new Exception("Falta parámetro requerido");
        }
        try {
            $this->_gatewayMgr->disconnectClient(array('name' => $name));
            $this->_flashMessenger->addSuccess("Cliente desconectado con éxito");
        } catch (Core_Service_Exception $ex) {
            $this->_flashMessenger->addError($ex->getMessage());
        }
        
        $this->_forward('listconnections');
    }
    
    public function enableaccessAction() {
        try {
            $this->_gatewayMgr->enableAccess();
        } catch (Core_Service_Exception $ex) {
            $this->_flashMessenger->addError($ex->getMessage());
        }
        
        $this->_forward('managestatus');
    }
    
    public function disableaccessAction() {
        try {
            $this->_gatewayMgr->disableAccess();
        } catch (Core_Service_Exception $ex) {
            $this->_flashMessenger->addError($ex->getMessage());
        }
        
        $this->_forward('managestatus');
    }
    
    public function reloadiptablesrulesAction() {
        try {
            $this->_gatewayMgr->reloadIptablesRules();
            $this->_flashMessenger->addSuccess("Reglas recargadas");
        } catch (Core_Service_Exception $ex) {
            $this->_flashMessenger->addError($ex->getMessage());
        }
        $this->_forward('managestatus');
    }
    
    public function startAction() {
        try {
            $this->_gatewayMgr->start();
        } catch (Core_Service_Exception $ex) {
            $this->_flashMessenger->addError($ex->getMessage());
        }
        $this->_forward('managestatus');
    }
    
    public function stopAction() {
        try {
            $this->_gatewayMgr->stop();
        } catch (Core_Service_Exception $ex) {
            $this->_flashMessenger->addError($ex->getMessage());
        }
        $this->_forward('managestatus');
    }
    
    public function listlastloginsAction() {
        $args = array();
        $number = $this->getRequest()->getParam('number');
        if($number) {
            $args['number'] = $number;
        } else {
            $args['number'] = 10;
        }

        $this->view->actionTitle = "Histórico de conexiones";
        $this->view->lastLogins = $this->_gatewayMgr->listLastLogins($args);
        $this->view->number = $args['number'];
    }

    public function listuserloginsAction() {
        $name = $this->getRequest()->getParam('name');
        if(!$name) {
            throw new Exception("Falta parámetro requerido");
        }

        $args = array();
        $args['name'] = $name;
        $number = $this->getRequest()->getParam('number');
        if($number) {
            $args['number'] = $number;
        } else {
            $args['number'] = 10;
        }

        $this->view->actionTitle = "Histórico de conexiones";
        $this->view->lastLogins = $this->_gatewayMgr->listClientHistory($args);
        $this->view->number = $args['number'];
        $this->view->name = $name;
    }

    public function listiptableslogAction() {
        $args = array();
        $number = $this->getRequest()->getParam('number');
        if($number) {
            $args['number'] = $number;
        } else {
            $args['number'] = 5;
        }

        try {
            $this->view->actionTitle = "Log iptables gateway";
            $this->view->logIptables = nl2br($this->_gatewayMgr->viewIptablesLog($args));
            $this->view->number = $args['number'];
        } catch(Core_Service_Exception $ex) {
            $this->_flashMessenger->addError($ex->getMessage());
            return $this->_forward('managestatus');
        }
    }

    
    public function listconnectionsAction() {
        try {
            $clients = $this->_gatewayMgr->showClients();
        } catch (Core_Service_Exception $ex) {
            $this->_flashMessenger->addError($ex->getMessage());
        }
        
        $this->view->actionTitle = "Listado de conexiones activas";
        $this->view->clients = $clients;
    }

    
    public function listauditeventsAction() {
        $args = array();
        $number = $this->getRequest()->getParam('number');
        if($number) {
            $args['number'] = $number;
        } else {
            $args['number'] = 10;
        }

        $this->view->actionTitle = "Histórico de auditoría";
        $this->view->lastEvents = $this->_gatewayMgr->listAuditEvents($args);
        $this->view->number = $args['number'];
    }

}
