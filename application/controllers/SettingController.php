<?php

class SettingController extends Zend_Controller_Action
{
    public function init()
    {
        /* Initialize action controller here */ 	
    }

    /*
    public function indexAction()
    {    	
        $dbTable_site = new Application_Model_DbTable_Site();        
        //print_r($dbTable_link->getLinks());
        
		$paginator = Zend_Paginator::factory($dbTable_site->getSites());
		$paginator->setItemCountPerPage(10);
		$paginator->setCurrentPageNumber($this->_getParam('page'));
		$this->view->site = $paginator;			
    }
    
    
	public function addAction()
    {    	
    	$dbTable_site = new Application_Model_DbTable_Site();
    	
        $form_link = new Application_Form_Link(); 
        $form_link->setSite($dbTable_site->fetchAll()->toArray());
        $form_link->submit->setLabel('Добавить');
		$this->view->form = $form_link;
    }  
    */
    
	public function editAction()
    {
    	
    	/*
    	$page = new Zend_Navigation_Page_Mvc(array(
    		'label'         => 'Настройки',
            'controller'    => 'setting', 
            'action'        => 'edit', 
            'params' => array(    			
                'idsite' => $this->_getParam('idsite', 0)
                 )
            )
       	);

		$this->view->navigation()->getContainer()->findOneBy('controller','site')->addPage($page);
    	*/
    	    	
    	$dbTable_site = new Application_Model_DbTable_Site();
    	
        $form_setting = new Application_Form_Setting();         
        
        $form_setting->submit->setLabel('Сохранить');
		$this->view->form = $form_setting;

	     if ($this->getRequest()->isPost()) {
	         $formData = $this->getRequest()->getPost();
	     
	         //$form_setting->getValue('delete');
	         
	         if ($form_setting->isValid($formData)) {
	         	
	         	if($form_setting->delete->isChecked()){
	         		//$googlerank = 20;
	         		$this->_helper->redirector('delete', 'site',  '', $this->_getAllParams());	         		
	         	}
	         		         	
				$idsetting = (int)$form_setting->getValue('idsetting');				
				$googlerank = $form_setting->getValue('googlerank');
				$yandexrank = $form_setting->getValue('yandexrank');
				$maxlinksday = $form_setting->getValue('maxlinksday');
				$minpageview = $form_setting->getValue('minpageview');
				$maxquerylength = $form_setting->getValue('maxquerylength');
				$linking = $form_setting->getValue('linking');
				$maxquerylinks = $form_setting->getValue('maxquerylinks');
				$maxpagelinks = $form_setting->getValue('maxpagelinks');
				$querystatus = $form_setting->getValue('querystatus');
				$numberlinks = $form_setting->getValue('numberlinks');
				/*$pagestatus = $form_setting->getValue('pagestatus');*/
				
				$siteid = (int)$form_setting->getValue('siteid');
				$host = $form_setting->getValue('host');
				
				//$linklistclass = $form_setting->getValue('linklistclass');
				//$linkitemclass = $form_setting->getValue('linkitemclass');
				//$linknofound = $form_setting->getValue('linknofound');
				$multisite_linking = $form_setting->getValue('multisite_linking');
				$multisite_maxquerylinks = $form_setting->getValue('multisite_maxquerylinks');
	             	             
	            $dbTable_setting = new Application_Model_DbTable_Setting();
	            $dbTable_setting->updateSetting($idsetting, $siteid, $googlerank, $yandexrank,
					$maxlinksday, $minpageview, $maxquerylength, $linking,
					$maxquerylinks, $maxpagelinks, $numberlinks, $querystatus,  /* $pagestatus,*/
					//$linklistclass, $linkitemclass,	$linknofound,
					$multisite_linking, $multisite_maxquerylinks);
					
				$dbTable_site->updateSite($siteid, $host);
	            
				//$this->_helper->redirector('index');
				
				$params = array('idsite' => $this->_getParam('idsite', 0));
				$this->_helper->redirector('edit', 'setting',  '', $params);
	            
	         } else {
	             $form_setting->populate($formData);
	         }
	     } else {
	     	
	         $idsite = $this->_getParam('idsite', 0);
	         
	         if ($idsite > 0) {
	         	
        		$form_setting->setSite($dbTable_site->getSite($idsite));
	         	
	         	$dbTable_setting = new Application_Model_DbTable_Setting();
	            $form_setting->populate($dbTable_setting->getSetting($idsite)->toArray());
	         }
	     }
    }
    
}







