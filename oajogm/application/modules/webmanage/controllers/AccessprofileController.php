<?php

class Webmanage_AccessprofileController extends Zend_Controller_Action
{
    /** @var LGC_Controller_Action_Helper_LGCFlashMessenger */
    protected $_flashMessenger = null;

    /** @var Core_Service_AccessProfileManager */
    protected $_profileMgr;

    /** @var Core_Service_NetworkResourceManager */
    protected $_rscMgr;
    
    public function init()
    {
        if(!Zend_Auth::getInstance()->hasIdentity()) {
             $this->_redirect('webmanage/login/index');
        }        
        
        $this->_flashMessenger = $this->_helper->getHelper('LGCFlashMessenger');
        
        $sm = LGC_Service_Manager::getInstance();
        $this->_profileMgr = $sm->getService('access_profile_manager');
        $this->_rscMgr = $sm->getService('network_resource_manager');
        
    }
    
    public function indexAction()
    {
        $this->_forward("list");
    }
    
    public function createAction() {
        $form = new Webmanage_Form_AccessProfile();
        $this->view->formTitle = "Crear nuevo perfil";
        $form->action->setLabel("Crear");
        
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            
            if ($form->isValid($formData)) {
                try {
                    $this->_profileMgr->createProfile($formData);
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
        
        $profile = $this->_profileMgr->showProfile(array('name' => $name));
        if(empty($profile)) {
            $this->_flashMessenger->addError("No se encuentra el perfil");
            return $this->_forward("list");
        }
        
        $form = new Webmanage_Form_AccessProfile();
        $this->view->formTitle = "Modificar perfil";
        $form->action->setLabel("Modificar");
                
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            
            if ($form->isValid($formData)) {
                try {
                    $this->_profileMgr->modifyProfile($formData);
                } catch (Core_Service_Exception $ex) {
                    $this->_flashMessenger->addError($ex->getMessage());
                    $this->view->form = $form;
                    return;
                }
                $this->_flashMessenger->addSuccess("Perfil modificado correctamente");
                return $this->_forward("list");
            }
        } else {
            $form->populate($profile);
            $form->showAuditInfo($profile['audit_info']);
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
            $this->_profileMgr->removeProfile(array('name' => $name));
            $this->_flashMessenger->addSuccess("Perfil eliminado correctamente");
        } catch (Core_Service_Exception $ex) {
            $this->_flashMessenger->addError($ex->getMessage());
        }
        
        $this->forward('list');
    }

    public function viewresourcesAction() {
        $name = $this->getRequest()->getParam('name');
        if(!$name) {
            throw new Exception("Falta parámetro requerido");
        }

        $profile = $this->_profileMgr->showProfile(array('name' => $name));
        if(empty($profile)) {
            $this->_flashMessenger->addError("No se encuentra el perfil");
            return $this->_forward("list");
        }
        $allResources = $this->_rscMgr->listResources();
        
        
        $resources = $profile['resources'];
        $available = array();
        foreach($allResources as $res1) {
            reset($resources);
            $found = false;
            foreach($resources as $res2) {
                if($res1['name'] == $res2['name']) {
                    $found = true;
                    break;
                }
            }
            if(!$found) {
                $available[] = $res1;
            }
        }
        
        $this->view->actionTitle = "Recursos de ".$profile['name'];
        $this->view->profile = $profile;
        $this->view->resources = $profile['resources'];
        $this->view->available = $available;
        
    }

    
    public function addresourceAction() {
        $prfname = $this->getRequest()->getParam('prfname');
        if(!$prfname) {
            throw new Exception("Falta parámetro requerido");
        }

        $addresources = $this->getRequest()->getParam('addresources');
        if(!$addresources) {
            throw new Exception("Falta parámetro requerido");
        }
        
        foreach($addresources as $rscname) {
            $args = array(
                'prfname' => $prfname,
                'rscname' => $rscname
            );
            try {
                $this->_profileMgr->addResource($args);
            } catch(Core_Service_Exception $ex) {
                $this->_flashMessenger->addError($ex->getMessage());
                return $this->_forward('list');
            }
        }

        $this->_forward('viewresources', NULL, NULL, array('name' => $prfname));
    }
    
    public function removeresourceAction() {
        $prfname = $this->getRequest()->getParam('prfname');
        if(!$prfname) {
            throw new Exception("Falta parámetro requerido");
        }

        $delresources = $this->getRequest()->getParam('delresources');
        if(!$delresources) {
            throw new Exception("Falta parámetro requerido");
        }
        
        foreach($delresources as $rscname) {
            $args = array(
                'prfname' => $prfname,
                'rscname' => $rscname
            );
            try {
                $this->_profileMgr->removeResource($args);
            } catch(Core_Service_Exception $ex) {
                $this->_flashMessenger->addError($ex->getMessage());
                return $this->_forward('list');
            }
        }
        
        $this->_forward('viewresources', NULL, NULL, array('name' => $prfname));
    }

    public function listAction() {
        $this->view->actionTitle = "Listado de perfiles";
        $this->view->profiles = $this->_profileMgr->listProfiles();
    }
    
}

