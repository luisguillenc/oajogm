<?php

class Webmanage_NetworkresourceController extends Zend_Controller_Action
{
    /** @var LGC_Controller_Action_Helper_LGCFlashMessenger */
    protected $_flashMessenger = null;

    /** @var Core_Service_NetworkResourceManager */
    protected $_netResMgr;
    
    public function init()
    {
        if(!Zend_Auth::getInstance()->hasIdentity()) {
             $this->_redirect('webmanage/login/index');
        }        
        
        $this->_flashMessenger = $this->_helper->getHelper('LGCFlashMessenger');
        
        $sm = LGC_Service_Manager::getInstance();
        $this->_netResMgr = $sm->getService('network_resource_manager');

    }

    public function indexAction()
    {
        $this->_forward("list");
    }
    
    public function createAction() {
        $type = $this->getRequest()->getParam('type');
        if(!$type) {
            throw new Exception("Falta parámetro requerido");
        }
        
        switch($type) {
            case 'host':
                $form = new Webmanage_Form_HostNetworkResource();
                break;
            case 'subnet':
                $form = new Webmanage_Form_SubnetNetworkResource();
                break;
            case 'range':
                $form = new Webmanage_Form_RangeNetworkResource();
                break;
            default:
                throw new Exception("Tipo no soportado");
        }

        $this->view->formTitle = "Crear nuevo $type";
        $form->action->setLabel("Crear");

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            
            if ($form->isValid($formData)) {
                try {
                    $this->_netResMgr->createResource($formData);
                } catch (Core_Service_Exception $ex) {
                    $this->_flashMessenger->addError($ex->getMessage());
                    $this->view->form = $form;
                    return;
                }
                $this->_flashMessenger->addSuccess("Recurso agregado correctamente");
                return $this->_forward("list");
            }
        }
        
        $this->view->form = $form;
    }
    
    public function modifyAction() {
        $name = $this->getRequest()->getParam('name');
        if(!$name) {
            throw new Exception("Falta parámetro requerido");
        }
        
        $resource = $this->_netResMgr->showResource(array('name' => $name));
        if(empty($resource)) {
            $this->_flashMessenger->addError("No se encuentra el recurso");
            return $this->_forward("list");
        }
        
        switch($resource['type']) {
            case 'host':
                $form = new Webmanage_Form_HostNetworkResource();
                break;
            case 'subnet':
                $form = new Webmanage_Form_SubnetNetworkResource();
                break;
            case 'range':
                $form = new Webmanage_Form_RangeNetworkResource();
                break;
            default:
                throw new Exception("Tipo no soportado");
        }
        
        $this->view->formTitle = "Modificar recurso";
        $form->action->setLabel("Modificar");
        
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            
            if ($form->isValid($formData)) {
                try {
                    $this->_netResMgr->modifyResource($formData);
                } catch (Core_Service_Exception $ex) {
                    $this->_flashMessenger->addError($ex->getMessage());
                    $this->view->form = $form;
                    return;
                }
                $this->_flashMessenger->addSuccess("Recurso modificado correctamente");
                return $this->_forward("list");
            }
        } else {
            $form->populate($resource);
            $form->showAuditInfo($resource['audit_info']);
            $form->getElement('name')->setAttrib("readonly", true);            
        }
        
        $this->view->form = $form;
    }
    
    public function removeAction() {
        $name = $this->getRequest()->getParam('name');
        if(!$name) {
            throw new Exception("Falta parámetro requerido");
        }
        
        try {
            $this->_netResMgr->removeResource(array('name' => $name));
            $this->_flashMessenger->addSuccess("Recurso eliminado correctamente");
        } catch (Core_Service_Exception $ex) {
            $this->_flashMessenger->addError($ex->getMessage());
        }
        $this->forward('list');
    }
    
    public function listAction() {
        $this->view->actionTitle = "Listado de recursos";
        $this->view->resources = $this->_netResMgr->listResources();
    }

}
