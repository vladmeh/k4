<?php

class Application_Form_Link extends Zend_Form
{
    public function init()
    {
        $this->setName('link');

        $idlink = new Zend_Form_Element_Hidden('idlink');
        $idlink->addFilter('Int');
        
        $host = new Zend_Form_Element_Select('host');
        $host->setLabel('Сайт');

        $name = new Zend_Form_Element_Text('name');        
        $name->setLabel('Текст ссылки')        
        	->addFilter('StripTags')
        	->addFilter('StringTrim');
        	
        $relative = new Zend_Form_Element_Text('relative');        
        $relative->setLabel('Относительный адресс')        
        	->addFilter('StripTags')
        	->addFilter('StringTrim');

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setAttrib('id', 'submitbutton');

        $this->addElements(array($idlink, $host, $relative, $name, $submit));
    }
    
	public function setSite(array $sites)
    {
        $select = $this->getElement('host');
        
        //$select->addMultiOption('', 'Все');
        
        foreach ($sites as $site) {
            $select->addMultiOption($site['idsite'], $site['host']);
        }
    }
 
    
}