<?php

class Webmanage_Form_AccessClient extends Webmanage_Form_AuditInfoForm
{    
    public function init()
    {
        $this->setName("access_client");
        $this->setMethod('post');

        $this->addElement('text', 'name', array(
            'order' => 1,
            'filters' => array('stringTrim'),
            'validators' => array(
                array('StringLength', false, array(0, 45)),
                array('Regex', true, "/^[a-z][a-z0-9_]{0,44}$/"),
            ),
            'required' => true,
            'label' => 'Name',
            'size' => 20,
            )
        );

        $this->addElement('text', 'ipaddr', array(
            'order' => 11,
            'required' => false,
            'readonly' => true,
            'label' => 'VPN Ip',
            'size' => 20,
            )
        );
        
        $this->addElement('select', 'prfname', array(
            'order' => 12,
            'required'   => true,
            'label'      => 'Perfil',
            )
        );

        $this->addElement('checkbox', 'locked', array(
            'order' => 13,
            'required'   => true,
            'value' => 1,
            'label'      => 'Bloqueado',
            )
        );

        $this->addElement('checkbox', 'iptableslogged', array(
            'order' => 14,
            'required'   => true,
            'value' => 0,
            'label'      => 'Log iptables',
            )
        );
        
        
        $this->addElement('textarea', 'desc', array(
            'order' => 15,
            'filters'    => array('StringTrim'),
            'required'   => false,
            'label'      => 'Description',
            'cols'       => 30,
            'rows'      => 3
            )
        );
        
        $this->addElement('submit', 'action', array(
            'order' => 20,
            'required' => false,
            'ignore'   => true,
            'label'    => ""
        ));
    }
}
