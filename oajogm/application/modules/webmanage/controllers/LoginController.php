<?php

class Webmanage_LoginController extends Zend_Controller_Action
{

    /** @var LGC_Controller_Action_Helper_LGCFlashMessenger */
    protected $_flashMessenger = null;

    
    public function init()
    {
        $this->_flashMessenger = $this->_helper->getHelper('LGCFlashMessenger');
    }

    public function indexAction()
    {
        // Get our authentication adapter and check credentials
        $adapter = new LGC_Auth_ModApache();
        $auth    = Zend_Auth::getInstance();
        $result  = $auth->authenticate($adapter);
        
        if (!$result->isValid()) {
            $this->_flashMessenger->addError("No se pudo autenticar al usuario");
            throw new Exception("No se pudo autenticar al usuario");
        }

        // We're authenticated! Redirect to the home page
        $this->_helper->redirector('index', 'index');

    }

    public function logoutAction()
    {
        Zend_Auth::getInstance()->clearIdentity();
        $this->_flashMessenger->addError('Identidad borrada');
//        $this->_helper->redirector('index'); // back to login page
    }


}
