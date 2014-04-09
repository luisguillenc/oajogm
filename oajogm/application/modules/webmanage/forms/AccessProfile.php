<?php

class Webmanage_Form_AccessProfile extends Webmanage_Form_AuditInfoForm
{    
    public function init()
    {
        $this->setName("access_profile");
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

        $this->addElement('textarea', 'desc', array(
            'order' => 10,
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
