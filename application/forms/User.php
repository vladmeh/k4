<?php

class Application_Form_User extends Zend_Form
{
    public function init()
    {
        $this->setName('user');

        $iduser = new Zend_Form_Element_Hidden('iduser');
        $iduser->addFilter('Int');
       	
        $username = new Zend_Form_Element_Text('username');        
        $username->setLabel('Логин')        
        	->addFilter('StripTags')
        	->addFilter('StringTrim');

        $password = new Zend_Form_Element_Text('password');
        $password->setLabel('Пароль')
        	->addFilter('StripTags')
        	->addFilter('StringTrim');
        
        $role = new Zend_Form_Element_Select('role');        
        $role->setLabel('Роль')
        	->setRequired(true);
        	
        $role->addMultiOption('admin', 'admin');

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setAttrib('id', 'submitbutton');
        $submit->setAttrib('class', 'btn btn-primary');

        $this->addElements(array($iduser, $username, $password, $role, $submit));
        
        // Группа полей связанных с личной информацией
        $this->addDisplayGroup(
            array('iduser', 'username', 'password', 'role'),
            'authGroup',
            array(
                'legend' => 'Натройка авторизации'
            )
        );
        
        // Группа полей кнопок
        $this->addDisplayGroup(
            array('submit'), 'buttonsGroup'
        );
    }
        
}