<?php

class Application_Form_Page extends Zend_Form
{
    public function init()
    {
        $this->setName('page');

        $idpage = new Zend_Form_Element_MultiCheckbox('idpage');

        $status = new Zend_Form_Element_Select('status');        
        $status->setLabel('Статус')
        	->setRequired(true);
        	
        $status->addMultiOption('OK', 'OK');
        $status->addMultiOption('LINK', 'LINK');
        $status->addMultiOption('PROMO', 'PROMO');
        $status->addMultiOption('DEL', 'DEL');

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setAttrib('id', 'submitbutton');

        $this->addElements(array($idpage, $status, $submit));
    }
        
}