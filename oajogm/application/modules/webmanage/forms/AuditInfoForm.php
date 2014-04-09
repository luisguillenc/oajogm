<?php

class Webmanage_Form_AuditInfoForm extends Zend_Form
{
    public function showAuditInfo($info) {
        $this->addElement('text', 'created', array(
            'order' => 2,
            'label' => 'Created',
            'required' => false,
            'value' => $info['created'],
            'readonly' => true
        ));
        $this->addElement('text', 'created_by', array(
            'order' => 3,
            'label' => 'Created by',
            'required' => false,
            'value' => $info['created_by'],
            'readonly' => true
        ));
        $this->addElement('text', 'updated', array(
            'order' => 4,
            'label' => 'Updated',
            'required' => false,
            'value' => $info['updated'],
            'readonly' => true
        ));
        $this->addElement('text', 'updated_by', array(
            'order' => 5,
            'label' => 'Updated by',
            'required' => false,
            'value' => $info['updated_by'],
            'readonly' => true
        ));
    }

}
