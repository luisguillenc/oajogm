<?php

class Webmanage_IndexController extends Zend_Controller_Action
{

    /** @var LGC_Controller_Action_Helper_LGCFlashMessenger */
    protected $_flashMessenger = null;

    public function init()
    {
        if(!Zend_Auth::getInstance()->hasIdentity()) {
             $this->_redirect('webmanage/login/index');
        }
        
        $this->_flashMessenger = $this->_helper->getHelper('LGCFlashMessenger');        
    }
    
    public function indexAction()
    {
        // action body
    }


}

