<?php

class SiteController extends Zend_Controller_Action
{
    public function init()
    {
    	/* Initialize action controller here */
    }

    public function indexAction()
    {
    	$resources = Zend_Controller_Front::getInstance()->getParam('bootstrap')
	    	->getOption('resources');
	    	
	    $pageRange = $resources['frontController']['paginator']['pageRange'];
	    $itemCountPerPage = $resources['frontController']['paginator']['itemCountPerPage'];
	    unset($resources);
    	
        $dbTable_site = new Application_Model_DbTable_Site(); 
        
        //print_r($dbTable_link->getLinks());
        $sortField = $this->_getParam('sort_field', 0); 
        $sortOrder = $this->_getParam('sort_order', 'ASC');
        
		$paginator = Zend_Paginator::factory($dbTable_site->getPaginator($sortField, $sortOrder));
		$paginator->setItemCountPerPage($itemCountPerPage);
		$paginator->setCurrentPageNumber($this->_getParam('page'));
		$paginator->setPageRange($pageRange);
		$this->view->site = $paginator;			
    }
    
    /*
	public function addAction()
    {    	
    	$dbTable_site = new Application_Model_DbTable_Site();
    	
        $form_link = new Application_Form_Link(); 
        $form_link->setSite($dbTable_site->fetchAll()->toArray());
        $form_link->submit->setLabel('Добавить');
		$this->view->form = $form_link;
    }  
    */

	public function deleteAction()
    {
    	//$this->_helper->viewRenderer->setNoRender(true);
		//$this->_helper->layout->disableLayout();
    	
    	$dbTable_site = new Application_Model_DbTable_Site();
    	
    	$idsite = $this->_getParam('idsite', 0);    	
    	   	
    	if($idsite){
    	
    		$site = $dbTable_site->find($idsite);
    	
			$site->current()->delete();		
    	}
    	
    	$this->_helper->redirector('site', 'index');
    }
    
}







