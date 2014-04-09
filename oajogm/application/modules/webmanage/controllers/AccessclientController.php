<?php

class Webmanage_AccessclientController extends Zend_Controller_Action
{
    /** @var LGC_Controller_Action_Helper_LGCFlashMessenger */
    protected $_flashMessenger = null;

    /** @var Core_Service_AccessClientManager */
    protected $_clientMgr;

    /** @var Core_Service_AccessProfileManager */
    protected $_profileMgr;
    
    public function init()
    {
        if(!Zend_Auth::getInstance()->hasIdentity()) {
             $this->_redirect('webmanage/login/index');
        }        
        
        $this->_flashMessenger = $this->_helper->getHelper('LGCFlashMessenger');
        
        $sm = LGC_Service_Manager::getInstance();
        $this->_clientMgr = $sm->getService('access_client_manager');
        $this->_profileMgr = $sm->getService('access_profile_manager');
    }
    
    public function indexAction()
    {
        $this->_forward("list");
    }


    public function listAction() {
        $this->view->actionTitle = "Listado de clientes";
        $this->view->clients = $this->_clientMgr->listClients();
    }
    
    
    protected function _fillSelectProfiles(Webmanage_Form_AccessClient $form) {
        $profiles = $this->_profileMgr->listProfiles();
        if(empty($profiles)) {
            $this->_flashMessenger->addError("No existen perfiles de acceso");
            return $this->_forward("create", "accessprofile");
        }
        
        $prfElements = array( '' => 'Seleccione un perfil' );
        foreach($profiles as $profile) {
            $prfElements[$profile['name']] = $profile['name'];
        }
        $form->getElement('prfname')->addMultiOptions($prfElements);        
    }
    
    public function createAction() {
        $form = new Webmanage_Form_AccessClient();
        $this->view->formTitle = "Crear nuevo cliente";
        $form->action->setLabel("Crear");

        $this->_fillSelectProfiles($form);
        
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            
            if ($form->isValid($formData)) {
                try {
                    $client = $this->_clientMgr->createClient($formData);
                } catch (Core_Service_Exception $ex) {
                    $this->_flashMessenger->addError($ex->getMessage());
                    $this->view->form = $form;
                    return;
                }
                $this->_flashMessenger->addSuccess("Perfil creado correctamente");
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
        
        $client = $this->_clientMgr->showClient(array('name' => $name));
        if(empty($client)) {
            $this->_flashMessenger->addError("No se encuentra el cliente");
            return $this->_forward("list");
        }
        
        $form = new Webmanage_Form_AccessClient();
        $this->view->formTitle = "Modificar cliente";
        $form->action->setLabel("Modificar");
        $this->_fillSelectProfiles($form);
        
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            
            if ($form->isValid($formData)) {
                try {
                    $this->_clientMgr->modifyClient($formData);
                } catch (Core_Service_Exception $ex) {
                    $this->_flashMessenger->addError($ex->getMessage());
                    $this->view->form = $form;
                    return;
                }
                $this->_flashMessenger->addSuccess("Cliente modificado correctamente");
                return $this->_forward("list");
            }
        } else {

            $form->populate($client);
            $form->showAuditInfo($client['audit_info']);
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
            $this->_clientMgr->removeClient(array('name' => $name));
            $this->_flashMessenger->addSuccess("Cliente eliminado correctamente");
        } catch (Core_Service_Exception $ex) {
            $this->_flashMessenger->addError($ex->getMessage());
        }
        
        $this->forward('list');
    }
    


}
