<?php
class Application_Plugin_Navigation extends Zend_Controller_Plugin_Abstract
{    
    
    public function preDispatch(Zend_Controller_Request_Abstract $request) {
    	
    	
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
        
        
        if ($viewRenderer->view != null){        	
        	$viewRenderer->initView();                   	
        }
        
        $view = $viewRenderer->view; 

            // Create a new Navigation Object
            $container = new Zend_Navigation();            
            
            $idsite = $this->getRequest()->getParam('idsite', 0);
            
            $iduser = $this->getRequest()->getParam('iduser', 0);
            
            $idquery = $this->getRequest()->getParam('idquery', 0);
            
            $dinamicPages = array (); 
            $dinamicPages2 = array ();   
           
            
            if($idsite > 0){
            	
            	$dbTable_site = new Application_Model_DbTable_Site(); 
        
        		$site = $dbTable_site->getSite($idsite);
            
            	$dinamicPages = array(
            	
            	array(
	                 'controller' => 'site',
	                 'action'  => 'index',
	                 'label' => $site->host,
	                 'pages' =>	
            		 array (
            		 
	            		 array (
	                         'controller' => 'page',
	                         'action' => 'index',
	                         'label' => 'Страницы',
	                     	 'params' => array('idsite'=>$idsite),
	                     ),
	                     array (
	                         'controller' => 'link',
	                         'action' => 'index',
	                         'label' => 'Ссылки',
	                     	 'params' => array('idsite'=>$idsite),
	                     ),
	                     array (
	                         'controller' => 'query',
	                         'action' => 'index',
	                         'label' => 'Запросы',
	                     	 'params' => array('idsite'=>$idsite),
	                     	 'pages' => array (
	                     	     array (
	                                 'controller' => 'position',
	                                 'action' => 'index',
	                                 'label' => 'Позиция',
	                     	         'params' => array('idquery'=>$idquery),
	                     		 )	                     
	                     	 )
	                     ),
	                     array (
	                         'controller' => 'setting',
	                         'action' => 'edit',
	                         'label' => 'Настройки',
	                     	 'params' => array('idsite'=>$idsite),
	                     )                     
                	 )
                 ));                 
            }
            
            
            if($iduser>0){
            	
            	$dbTable_user = new Application_Model_DbTable_User(); 
            	
            	$user = $dbTable_user->getUser($iduser);
            	
            	$dinamicPages2 = array(
            	
            	array(
	                 'controller' => 'user',
	                 'action'  => 'edit',
	                 'label' => $user->username,
	                 'params' => array('iduser'=>$iduser)            		 
                 )
                 
               );            	            	
            }
            
            // Create the pages
            $pages = array(            
	            array(
		                 'controller' => 'index',
		                 'action'  => 'index',
		                 'label' => 'Главная',
	            		 'pages' => array(
				             array(
				                 'controller' => 'site',
				                 'action'  => 'index',
				                 'label' => 'Сайты',
				                 'pages' => $dinamicPages
				             ),
				             array(
				                 'controller' => 'user',
				                 'action'  => 'index',
				                 'label' => 'Пользователи',
				                 'pages' => $dinamicPages2	                         	 
				             ),
				             array(
				                 'controller' => 'auth',
				                 'action'  => 'logout',
				                 'label' => 'Выход',	                 
				             )
		        		)
		        	)
         	);

            // Set the pages to the navigation container
            $container->setPages($pages);

            // Set the active page
            //$activePage = $container->findByUri($request->getRequestUri());
            //$activePage->active = true;

            // Assign the navigation to the view
            $view->navigation($container);
    }
}