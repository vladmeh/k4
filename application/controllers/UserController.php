<?php

class UserController extends Zend_Controller_Action
{
    public function init()
    {
        /* Initialize action controller here */ 	
    }

    public function indexAction()
    {
        $dbTable_user = new Application_Model_DbTable_User();
        
		$paginator = Zend_Paginator::factory($dbTable_user->fetchAll());
		$paginator->setItemCountPerPage(10);
		$paginator->setCurrentPageNumber($this->_getParam('page'));
		$this->view->user = $paginator;		
    }
    
	public function editAction()
    {  	
    	
        $form_user = new Application_Form_User(); 
        $form_user->submit->setLabel('Сохранить');
		$this->view->form = $form_user;	

	     if ($this->getRequest()->isPost()) {
	         $formData = $this->getRequest()->getPost();
	         if ($form_user->isValid($formData)) {
	         	
				$iduser = (int)$form_user->getValue('iduser');
				$username = $form_user->getValue('username');
				$password = $form_user->getValue('password');
				$role = $form_user->getValue('role');
	             	             
	            $dbTable_user = new Application_Model_DbTable_User();
	            $dbTable_user->updateUser($iduser, $username, $password, $role);
	            
				//$this->_helper->redirector('index');
				$this->_helper->redirector('edit', 'user',  '', $this->_getAllParams());
	            
	         } else {
	             $form_user->populate($formData);
	         }
	     } else {
	     	
	         $iduser = $this->_getParam('iduser', 0);
	         
	         if ($iduser > 0) {
	             $dbTable_user = new Application_Model_DbTable_User();
	             $form_user->populate($dbTable_user->getUser($iduser)->toArray());
	         }
	     }
    }
        
	public function addAction()
	{	
	     $form_user = new Application_Form_User();
	     $form_user->submit->setLabel('Добавить');
	     $this->view->form = $form_user;
	
	     if ($this->getRequest()->isPost()) {
	         $formData = $this->getRequest()->getPost();
	         if ($form_user->isValid($formData)) {

	             $username = $form_user->getValue('username');
	             $password = $form_user->getValue('password');
	             $role = $form_user->getValue('role');
	             
	             $dbTable_user = new Application_Model_DbTable_User();
	             $dbTable_user->addUser($username, $password, $role);
	
	             $this->_helper->redirector('index');
	         } else {
	             $form_user->populate($formData);
	         }
	     }
	}
    
}







