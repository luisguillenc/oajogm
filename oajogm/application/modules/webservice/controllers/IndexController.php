<?php

class Webservice_IndexController extends Zend_Controller_Action
{

    public function init()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        
        $this->_checkAuth();
    }
    
    
    protected function _checkAuth() {
        $sm = LGC_Service_Manager::getInstance();
        $gwConf = $sm->getService('gw_conf');
        if($gwConf->isGatewaySharedKeyEnabled()) {
            $salt = $this->getRequest()->getParam('salt');
            if(!$salt) {
                throw new Exception("Falta parámetro requerido");
            }

            $hash = $this->getRequest()->getParam('hash');
            if(!$hash) {
                throw new Exception("Falta parámetro requerido");
            }
            
            if(!$gwConf->checkGatewaySharedKey($salt, $hash)) {
                throw new Exception("Error de autenticación del gateway");
            }
        }
    }
    
    public function notifydisconnectAction() {
        $name = $this->getRequest()->getParam('name');
        if(!$name) {
            throw new Exception("Falta parámetro requerido");
        }
        
        $sm = LGC_Service_Manager::getInstance();
        $cnMgr = $sm->getService('connection_manager');
        $cnMgr->notifyDisconnect($name);
        $this->getResponse()->setHeader('Content-Type', 'text/plain');
        $this->getResponse()->setBody("0");        
    }
    
    public function checkaccessAction() {
        $name = $this->getRequest()->getParam('name');
        if(!$name) {
            throw new Exception("Falta parámetro requerido");
        }
        
        $sm = LGC_Service_Manager::getInstance();
        $cnMgr = $sm->getService('connection_manager');
        
        if($cnMgr->checkAccess($name)) {
            $this->getResponse()->setHeader('Content-Type', 'text/plain');
            $this->getResponse()->setBody("0");
        } else {
            $this->getResponse()->setRawHeader('HTTP/1.1 404 Not Found');
            return;
        }
        
    }
    
    public function getclientconfigAction() {
        $name = $this->getRequest()->getParam('name');
        if(!$name) {
            throw new Exception("Falta parámetro requerido");
        }
        
        $sm = LGC_Service_Manager::getInstance();
        $cnMgr = $sm->getService('connection_manager');

        $configScript = $cnMgr->getClientConfigScript($name);
        $cnMgr->notifyConnect($name);
        
        $this->getResponse()->setHeader('Content-Type', 'text/plain');
        $this->getResponse()->setBody($configScript);
    }
    
    public function getiptablesscriptAction() {
        $sm = LGC_Service_Manager::getInstance();
        $cnMgr = $sm->getService('connection_manager');
        $sourceScript = $cnMgr->getIptablesScript();
        $this->getResponse()->setHeader('Content-Type', 'text/plain');
        $this->getResponse()->setBody($sourceScript);
    }

}

