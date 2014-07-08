<?php

class Application_Form_Page extends Zend_Form
{
    public function init()
    {
        $this->setName('page');

        $idpage = new Zend_Form_Element_Hidden('idpage');
        $idpage->addFilter('Int');

        $siteid = new Zend_Form_Element_Select('siteid');        
        $siteid->setLabel('Сайт')
        	->setRequired(true);
        	
        //$siteid->setAttrib('disabled', true);
        	
        $relative = new Zend_Form_Element_Text('relative');        
        $relative->setLabel('Относительный адресс')        
        	->addFilter('StripTags')
        	->addFilter('StringTrim');    

        $title = new Zend_Form_Element_Text('title');
        $title->setLabel('Заголовок')
        	->addFilter('StripTags')
        	->addFilter('StringTrim');

        $status = new Zend_Form_Element_Select('status');        
        $status->setLabel('Статус')
        	->setRequired(true);
        	
        $status->addMultiOption('OK', 'OK');
        $status->addMultiOption('LINK', 'LINK');
        $status->addMultiOption('PROMO', 'PROMO');
        $status->addMultiOption('DEL', 'DEL');

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setAttrib('id', 'submitbutton');

        $this->addElements(array($idpage, $siteid, $relative, $title, $status, $submit));
    }
    
	public function setSite(array $sites)
    {
        $select = $this->getElement('siteid');
        
        //$select->addMultiOption('', 'Все');
        
        foreach ($sites as $site) {
            $select->addMultiOption($site['idsite'], $site['host']);
        }
    }
        
}