<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{		
	private $_acl = null;
    private $_auth = null;
	
	protected function _initAutoload()
	{
		$autoloader = new Zend_Application_Module_Autoloader(array(
			'namespace' => '',
			'basePath'  => APPLICATION_PATH,
		));		
		
		return $autoloader;
	}
	
	protected function _initAcl()
    {
        // РЎРѕР·РґР°С‘Рј РѕР±СЉРµРєС‚ Zend_Acl
        $acl = new Zend_Acl();
        
        // Р”РѕР±Р°РІР»СЏРµРј СЂРµСЃСѓСЂСЃС‹ РЅР°С€РµРіРѕ СЃР°Р№С‚Р°,
        // РґСЂСѓРіРёРјРё СЃР»РѕРІР°РјРё СѓРєР°Р·С‹РІР°РµРј РєРѕРЅС‚СЂРѕР»Р»РµСЂС‹ Рё РґРµР№СЃС‚РІРёСЏ
        
        // СѓРєР°Р·С‹РІР°РµРј, С‡С‚Рѕ Сѓ РЅР°СЃ РµСЃС‚СЊ СЂРµСЃСѓСЂСЃ index
        $acl->addResource('index');       
        
        $acl->addResource('site');
        
        $acl->addResource('setting');
        
        $acl->addResource('link');
        
        $acl->addResource('page');
        
        $acl->addResource('query');

        $acl->addResource('user');  
        
        $acl->addResource('position');
        
        
        $acl->addResource('error');        
        
        $acl->addResource('auth');        
        
        $acl->addResource('login', 'auth');        
        
        $acl->addResource('logout', 'auth');        
        
        $acl->addRole('guest');        
        
        $acl->addRole('admin', 'guest');        
        
        $acl->allow('guest', 'index', array('index'));        
        
        $acl->allow('guest', 'auth', array('index', 'login', 'logout'));
        
        $acl->allow('guest', 'link', array('rss'));
        
        $acl->allow('admin', 'auth', array('logout'));
        
        $acl->allow('admin', 'site', array('index', 'delete'));
        
        $acl->allow('admin', 'setting', array('edit'));
        
        $acl->allow('admin', 'page', array('index', 'edit', 'export'));
        
        $acl->allow('admin', 'link', array('index', 'edit', 'export', 'rss'));
        
        $acl->allow('admin', 'query', array('index', 'edit', 'export'));
        
        $acl->allow('admin', 'position', array('index'));
        
        $acl->allow('admin', 'user', array('index', 'edit', 'add'));        
        
        $acl->allow('admin', 'error');
        
        $this->_acl = $acl;
        $this->_auth = Zend_Auth::getInstance();
        
        
        $fc = Zend_Controller_Front::getInstance();
        
        $fc->registerPlugin(new Application_Plugin_AccessCheck($acl, Zend_Auth::getInstance()));
    }
    
	protected function _initNavigation() 
	{
		$this->bootstrap('frontController');
        $fc = Zend_Controller_Front::getInstance();        
        $fc->registerPlugin(new Application_Plugin_Navigation());		
		
		/*
		$this->bootstrap("view");		
  		$view = $this->getResource('view');
				
		$config = new Zend_Config_Xml(APPLICATION_PATH . '/configs/navigation.xml','nav');
		$navigation = new Zend_Navigation($config);
		
		$view->navigation($navigation)->setAcl($this->_acl)
                                      ->setRole($this->_auth->getStorage()->read()->role);
        */
		
		//var_dump($this->bootstrap('view')->getResource('view')->getHelperPaths());		
	}
    
	/*
	// кэширование админки
	protected function _initCache() 
	{
		$this->bootstrap('frontController') ;
        $fc = Zend_Controller_Front::getInstance();
        $fc->registerPlugin(new Application_Plugin_Cache());

        $cache = Zend_Registry::get('cache');
        $cache->start();
	}
    */
}