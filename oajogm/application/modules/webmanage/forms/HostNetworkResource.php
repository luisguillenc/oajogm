<?php

class Webmanage_Form_HostNetworkResource extends Webmanage_Form_AuditInfoForm
{    
    public function init()
    {
        $this->setName("host_resource");
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
            'order' => 10,
            'filters' => array('stringTrim'),
            'validators' => array(
                array('StringLength', false, array(0, 45)),
                array('Regex', true, "/^([1-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])" .
                "(\.([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])){3}$/"),
            ),
            'required' => true,
            'label' => 'IP Address',
            'size' => 20,
            )
        );

        $this->addElement('textarea', 'desc', array(
            'order' => 11,
            'filters'    => array('StringTrim'),
            'required'   => false,
            'label'      => 'Description',
            'cols'       => 30,
            'rows'      => 3
            )
        );

        $this->addElement('hidden', 'type', array(
            'order' => 19,
            'value' => 'host')
                );
        
        $this->addElement('submit', 'action', array(
            'order' => 20,
            'required' => false,
            'ignore'   => true,
            'label'    => ""
        ));
    }
}
