<?php

class LinkController extends Zend_Controller_Action
{
    public function init()
    {
        /* Initialize action controller here */    	 	
    }

    public function indexAction()
    {
    	    	
        $dbTable_link = new Application_Model_DbTable_Link(); 

        $form_link = new Application_Form_Link();
        
        if ($this->getRequest()->isPost()) {

        	$formData = $this->getRequest()->getPost();
    		
    		$form_link->isValid($formData);
    		
    		$idlink = $form_link->getValue('idlink');
			
	        $dbTable_link->deleteLinks($idlink);
	        
	        //$this->_helper->redirector('index');

	        $params =
			        array('idsite' => $this->_getParam('idsite', 0),
	        			'filter_field' => $this->_getParam('filter_field'),
	        			'filter_value' => $this->_getParam('filter_value'),
			        	'sort_field' => $this->_getParam('sort_field'),
			        	'sort_order' => $this->_getParam('sort_order'),
			        );
	        
	        $this->_helper->redirector('index', 'link',  '', $params);		
			
        }else{
        	
        	$resources = Zend_Controller_Front::getInstance()->getParam('bootstrap')
    		->getOption('resources');
    	
	    	$pageRange = $resources['frontController']['paginator']['pageRange'];
	    	$itemCountPerPage = $resources['frontController']['paginator']['itemCountPerPage'];
	    	unset($resources);
        	
        	$idsite = $this->_getParam('idsite', 0);
        	 
        	$sortField = $this->_getParam('sort_field', 0); 
       		$sortOrder = $this->_getParam('sort_order', 'ASC');   
        	
        	$paginator = Zend_Paginator::factory($dbTable_link->getPaginator($idsite, $sortField, $sortOrder));
			$paginator->setItemCountPerPage($itemCountPerPage);
			$paginator->setCurrentPageNumber($this->_getParam('page'));
			$paginator->setPageRange($pageRange);
			$this->view->link = $paginator;
        }
    }    
        
    /*
	public function editAction()
    {
    	$dbTable_site = new Application_Model_DbTable_Site();
   	
        $form_link = new Application_Form_Link(); 
        $form_link->setSite($dbTable_site->fetchAll()->toArray());
        $form_link->submit->setLabel('Редактировать');
		$this->view->form = $form_link;	

	     if ($this->getRequest()->isPost()) {
	         $formData = $this->getRequest()->getPost();
	         if ($form_link->isValid($formData)) {
	         	
	         	$idlink = (int)$form_link->getValue('idlink');
	         	$siteid = (int)$form_link->getValue('host');
	            $relative = $form_link->getValue('name');
	            $title = $form_link->getValue('title');
	             	             
	            $dbTable_link = new Application_Model_DbTable_Link();
	            $dbTable_link->updateLink($idlink, $siteid, $relative, $title);
	
	             $this->_helper->redirector('index');
	         } else {
	             $form_link->populate($formData);
	         }
	     } else {
	         $idlink = $this->_getParam('idlink', 0);
	         
	         if ($idlink > 0) {
	             $dbTable_link = new Application_Model_DbTable_Link();
	             $form_link->populate($dbTable_link->getLink($idlink)->toArray());
	         }
	     }
    }
    */    
    
	public function exportAction()
    {   
    	$this->_helper->viewRenderer->setNoRender(true);
		$this->_helper->layout->disableLayout();
		
		header("Content-Type: application/octet-stream");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("content-disposition: attachment;filename=link" . time() . ".xls");
    	
        $dbTable_link = new Application_Model_DbTable_Link();       
        $idsite = $this->_getParam('idsite', 0);
        
        //$filterField = $this->_getParam('filter_field', 0); 
 		//$filterValue = $this->_getParam('filter_value', 0);
        
        $sortField = $this->_getParam('sort_field', 0); 
        $sortOrder = $this->_getParam('sort_order', 'ASC');
        
        //$this->_response->setHttpResponseCode(200);
        //$this->_response->setHeader('Content-Type', 'application/octet-stream', true);
        //$this->_response->setHeader('Content-Disposition', 'attachment; filename="link.xls"', true);
        
        $links = $dbTable_link->getLinks($idsite, $sortField, $sortOrder);
        
        $content = "";
        
        foreach($links as $link){        	        	
        	$link = (array) $link;
        	
        	echo iconv('utf-8', 'windows-1251', implode("	", $link));            		
			echo "\n";
        }
    }
    
	public function rssAction()
    {    	
    	$idsite = $this->_getParam('idsite', 0);
    	
    	$this->_helper->viewRenderer->setNoRender(true);
		$this->_helper->layout->disableLayout();
		
		$dbTable_link = new Application_Model_DbTable_Link();		
		
    	$feed = new Zend_Feed_Writer_Feed;
    	$feed->setFeedLink("http://test.loc/link/rss", 'rss');
        $feed->setTitle('Links for indexation');
        $feed->setLink("http://test.loc/link/rss");
        $feed->setDateModified(time());
        $feed->setDescription('Links for indexation');
        
        foreach($dbTable_link->getLinkForIndexation($idsite) as $link){
        	
        	try{
	            $entry = $feed->createEntry();         
	                        
	            $entry->setTitle($link->title); //$link->title
	
				$entry->setLink("http://" . $link->host . $link->relative);
				
				/*
				$entry->addAuthor(array(			
				    'name'  => 'Koldunschik',			
				    'email' => 'paddy@example.com',			
				    'uri'   => 'http://www.example.com',			
				));	
				*/		
				
				$date = new Zend_Date($link->date, 'YYYY-MM-dd HH:mm:ss');
	            $timeStamp = $date->getTimestamp();
				
				$entry->setDateModified($timeStamp);			
				$entry->setDateCreated($timeStamp);			
				
				$entry->setDescription($link->name);
				
				//$entry->setContent($link->name);
	            
	            $feed->addEntry($entry);     
        	}catch(Exception $ex){
        		
        	}
        }

        echo $feed->export('rss');
    }
    
    
    
}







