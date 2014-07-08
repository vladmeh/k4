<?php

class QueryController extends Zend_Controller_Action
{
    public function init()
    {
    	
    }

    public function indexAction()
    {    	
    	$dbTable_query = new Application_Model_DbTable_Query();        
	    $idsite = $this->_getParam('idsite', 0);
    	
    	$form_query = new Application_Form_Query();
    	//$form_query->setIdQuery();
    	//$this->view->form = $form_query;
    	
    	$this->view->form = $form_query;
    	
    	if ($this->getRequest()->isPost()) {
    		
    		$formData = $this->getRequest()->getPost();
    		
    		$form_query->isValid($formData);
    		//if($form_query->isValid($formData)){    		
	         	
				$idquery = $form_query->getValue('idquery');
				$status = $form_query->getValue('status');
				
				
    			$dbTable_link = new Application_Model_DbTable_Link();
				
				//чистим зависимые таблицы
				switch ($status) {
				    case "DEL":				        
				        $dbTable_link->deleteLinksOnQueries($idquery);				        			        
				        break;
				}
				
				//print_r($idquery);
		                         
		        //$dbTable_query = new Application_Model_DbTable_Query();
		        $dbTable_query->updateQueries($idquery, $status);
		        
		        //$this->_helper->redirector('index');
		        
		        //$params = array('idsite' => 6);
		        $params =
			        array('idsite' => $this->_getParam('idsite', 0),
	        			'filter_field' => $this->_getParam('filter_field'),
	        			'filter_value' => $this->_getParam('filter_value'),
			        	'sort_field' => $this->_getParam('sort_field'),
			        	'sort_order' => $this->_getParam('sort_order'),
			        );
				$this->_helper->redirector('index', 'query',  '', $params);
		        
			
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
	        
	        $this->view->statuses = $dbTable_query->getStatuses($idsite);
	        
			$paginator = Zend_Paginator::factory($dbTable_query->getPaginator($idsite, $filterField, $filterValue, $sortField, $sortOrder));
			$paginator->setItemCountPerPage($itemCountPerPage);
			$paginator->setCurrentPageNumber($this->_getParam('page'));
  			$paginator->setPageRange($pageRange);
  			
			$this->view->query = $paginator;
		
	    }
    }
    
	public function exportAction()
    {
    	$this->_helper->viewRenderer->setNoRender(true);
		$this->_helper->layout->disableLayout();
		
		//$this->_response->setHttpResponseCode(200);
        //$this->_response->setHeader('Content-Type', 'application/octet-stream', true);
        //$this->_response->setHeader('Content-Disposition', 'attachment; filename="query.xls"', true);
        
		header("Content-Type: application/octet-stream");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("content-disposition: attachment;filename=query" . time() . ".xls"); 
    	
        $dbTable_query = new Application_Model_DbTable_Query();        
        $idsite = $this->_getParam('idsite', 0);
        
        $filterField = $this->_getParam('filter_field', 0); 
 		$filterValue = $this->_getParam('filter_value', 0);
        
        $sortField = $this->_getParam('sort_field', 0); 
        $sortOrder = $this->_getParam('sort_order', 'ASC');
        
        $queries = $dbTable_query->getQueries($idsite, $filterField, $filterValue, $sortField, $sortOrder);        
        
        foreach($queries as $query){        	        	
        	$query = (array) $query;
        	echo iconv('utf-8', 'windows-1251', implode("	", $query));        		
			echo "\n";      	
        }
       
    }
    
	public function editAction()
    {
        $form_query = new Application_Form_Query(); 
        $form_query->submit->setLabel('Сохранить');
		$this->view->form = $form_query;	

	     if ($this->getRequest()->isPost()) {
	         $formData = $this->getRequest()->getPost();
	         if ($form_query->isValid($formData)) {
	         	
				$idquery = (int)$form_query->getValue('idquery');
				$status = $form_query->getValue('status');
	                         
	            $dbTable_query = new Application_Model_DbTable_Query();
	            $dbTable_query->updateQuery($idquery, $status);
	            
				$this->_helper->redirector('index');
	            
	         } else {
	             $form_query->populate($formData);
	         }
	     } else {
	     	
	         $idquery = $this->_getParam('idquery', 0);
	         
	         if ($idquery > 0) {
	             $dbTable_query = new Application_Model_DbTable_Query();
	             $form_query->populate($dbTable_query->getQuery($idquery)->toArray());
	         }
	     }
    }
    
}







