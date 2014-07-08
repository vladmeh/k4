<?php

class Application_Form_Query extends Zend_Form
{
    public function init()
    {
        $this->setName('query');

        $idquery = new Zend_Form_Element_MultiCheckbox('idquery');
        //$idquery->addFilter('Int');
        
        //$idquery->addMultiOption(19, 19);
        
        $status = new Zend_Form_Element_Select('status');        
        $status->setLabel('Статус')
        	->setRequired(true);
        	
        $status->addMultiOption('OK', 'OK');
        $status->addMultiOption('WAIT', 'WAIT');
        $status->addMultiOption('DEL', 'DEL');
        
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setAttrib('id', 'submitbutton');

        $this->addElements(array($idquery, $status, $submit));        
    }
    
}