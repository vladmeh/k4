<?php

class PositionController extends Zend_Controller_Action
{
    public function init()
    {
    	/*
    	$page = new Zend_Navigation_Page_Mvc(array(
    		'label'         => 'Запросы',
            'controller'    => 'query', 
            'action'        => 'index', 
            'params' => array(    			
                'idsite' => $this->_getParam('idsite', 0)
                 )
            )
       	);
      	
       	$this->view->navigation()->getContainer()->findOneBy('controller','site')->addPage($page);    	
    	
        
    	$page = new Zend_Navigation_Page_Mvc(array(
    		'label'         => 'Позиции',
            'controller'    => 'position', 
            'action'        => 'index', 
            'params' => array(    			
                'idquery' => $this->_getParam('idquery', 0)
                 )
            )
       	);
      	
       	$this->view->navigation()->getContainer()->findOneBy('controller','query')->addPage($page);	
       	*/
    }

    public function indexAction()
    {    	
    	$resources = Zend_Controller_Front::getInstance()->getParam('bootstrap')
    	->getOption('resources');
    	
	    $pageRange = $resources['frontController']['paginator']['pageRange'];
	    $itemCountPerPage = $resources['frontController']['paginator']['itemCountPerPage'];
	    unset($resources);
    	
        $dbTable_position = new Application_Model_DbTable_Position();
        $idquery = $this->_getParam('idquery', 0);
        
		$paginator = Zend_Paginator::factory($dbTable_position->getPaginator($idquery));
		$paginator->setItemCountPerPage($itemCountPerPage);
		$paginator->setCurrentPageNumber($this->_getParam('page'));
		$paginator->setPageRange($pageRange);
		$this->view->position = $paginator;		
    }
}







