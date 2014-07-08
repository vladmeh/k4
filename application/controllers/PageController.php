<?php

class PageController extends Zend_Controller_Action
{
    public function init()
    {
		    	
    }

    public function indexAction()
    {
    	
    	$dbTable_page = new Application_Model_DbTable_Page();
        $idsite = $this->_getParam('idsite', 0);

        $form_page = new Application_Form_Page(); 
        
        if ($this->getRequest()->isPost()) {
    		
    		$formData = $this->getRequest()->getPost();
    		
    		$form_page->isValid($formData);
    		//if($form_query->isValid($formData)){
	         	
				$idpage = $form_page->getValue('idpage');
				$status = $form_page->getValue('status');
				
				$dbTable_link = new Application_Model_DbTable_Link();
				$dbTable_query = new Application_Model_DbTable_Query();
				
				//чистим зависимые таблицы
				switch ($status) {
				    case "DEL":
				        $dbTable_link->deleteLinksPerPages($idpage);
				        //$dbTable_link->deletePromoLinks($idpage);
				        $dbTable_query->deletePagesQueries($idpage);
				        break;
				        
				    case "PROMO":
				        $dbTable_link->deleteLinksPerPages($idpage);
				        break;
				        
				    case "LINK":				        
				        //$dbTable_link->deletePromoLinks($idpage);
				        $dbTable_query->deletePagesQueries($idpage);
				        break;
				}
												
		        //$dbTable_query = new Application_Model_DbTable_Query();
		        $dbTable_page->updatePages($idpage, $status);
		        
		        $params =
			        array('idsite' => $this->_getParam('idsite', 0),
	        			'filter_field' => $this->_getParam('filter_field'),
	        			'filter_value' => $this->_getParam('filter_value'),
			        	'sort_field' => $this->_getParam('sort_field'),
			        	'sort_order' => $this->_getParam('sort_order'),
			        );
		        
				$this->_helper->redirector('index', 'page',  '', $params);
    		//} else {
    		//	$form_query->populate($formData);
	        //}

	    } else {
	    	$resources = Zend_Controller_Front::getInstance()->getParam('bootstrap')
    		->getOption('resources');
    	
	    	$pageRange = $resources['frontController']['paginator']['pageRange'];
	    	$itemCountPerPage = $resources['frontController']['paginator']['itemCountPerPage'];
	    	unset($resources);	
        
	        $filterField = $this->_getParam('filter_field', 0); 
	 		$filterValue = $this->_getParam('filter_value', 0);
	        
	        $sortField = $this->_getParam('sort_field', 0); 
	        $sortOrder = $this->_getParam('sort_order', 'ASC');
	        
	        $this->view->statuses = $dbTable_page->getStatuses($idsite);
	        
			$paginator = Zend_Paginator::factory($dbTable_page->getPaginator($idsite, $filterField, $filterValue, $sortField, $sortOrder));
			$paginator->setItemCountPerPage($itemCountPerPage);
			$paginator->setCurrentPageNumber($this->_getParam('page'));
			$paginator->setPageRange($pageRange);
			
			$this->view->page = $paginator;
	    }
	    
    } 

	public function exportAction()
    {
    	$this->_helper->viewRenderer->setNoRender(true);
		$this->_helper->layout->disableLayout();
    	
        $dbTable_page = new Application_Model_DbTable_Page();
        $idsite = $this->_getParam('idsite', 0);
        
        $filterField = $this->_getParam('filter_field', 0); 
 		$filterValue = $this->_getParam('filter_value', 0);
        
        $sortField = $this->_getParam('sort_field', 0); 
        $sortOrder = $this->_getParam('sort_order', 'ASC');
        
        $this->_response->setHttpResponseCode(200);
        $this->_response->setHeader('Content-Type', 'application/octet-stream', true);
        $this->_response->setHeader('Content-Disposition', 'attachment; filename="page.xls"', true);
        
        $pages = $dbTable_page->getPages($idsite, $filterField, $filterValue, $sortField, $sortOrder);;
        
        foreach($pages as $page){        	        	
        	$page = (array) $page;        	
        	echo implode("	", $page);			
			echo "\n";
        }
    }
    
	public function editAction()
    {
    	$dbTable_site = new Application_Model_DbTable_Site();
    	
        $form_page = new Application_Form_Page(); 
        $form_page->setSite($dbTable_site->fetchAll()->toArray());
        $form_page->submit->setLabel('Редактировать');
		$this->view->form = $form_page;	

	     if ($this->getRequest()->isPost()) {
	         $formData = $this->getRequest()->getPost();
	         if ($form_page->isValid($formData)) {
	         	
	         	$idpage = (int)$form_page->getValue('idpage');
	         	$siteid = (int)$form_page->getValue('siteid');
	            $relative = $form_page->getValue('relative');
	            $title = $form_page->getValue('title');
	            $status = $form_page->getValue('status');
	             	             
	            $dbTable_page = new Application_Model_DbTable_Page();
	            $dbTable_page->updatePage($idpage, $siteid, $relative, $title, $status);
	
	             $this->_helper->redirector('index');
	         } else {
	             $form_page->populate($formData);
	         }
	     } else {
	         $idpage = $this->_getParam('idpage', 0);
	         
	         if ($idpage > 0) {
	             $dbTable_page = new Application_Model_DbTable_Page();
	             $form_page->populate($dbTable_page->getPage($idpage)->toArray());
	         }
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
            $entry = $feed->createEntry();
            
            //$entry->$method($value);
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
        }

        echo $feed->export('rss');
    }
    
    
}







