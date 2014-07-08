<?php

class Application_Form_Link extends Zend_Form
{
    public function init()
    {
        $this->setName('link');
        
        $idlink = new Zend_Form_Element_MultiCheckbox('idlink');
        //$idquery->addFilter('Int');        
        
        $action = new Zend_Form_Element_Select('action');        
        $action->setLabel('Действие')
        	->setRequired(true);
        	
        $action->addMultiOption('delete', 'Удалить');

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setAttrib('id', 'submitbutton');

        $this->addElements(array($idlink, $action, $submit));
    }
}