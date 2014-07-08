<?php
 
class Application_Plugin_Cache extends Zend_Controller_Plugin_Abstract
{
    public function __construct()
    {
        $frontendOptions = array(
	        'lifetime' => 3600,
	        'automatic_serialization' => false,
        	//'debug_header' => true,
	        'default_options' => array(
	            'cache_with_get_variables' => true,
	            'cache_with_post_variables' => true,
	            'cache_with_session_variables' => true,
	            'cache_with_files_variables' => true,
	            'cache_with_cookie_variables' => true,
	            'make_id_with_get_variables' => true,
	            'make_id_with_post_variables' => true,
	            'make_id_with_session_variables' => true,
	            'make_id_with_files_variables' => true,
	            'make_id_with_cookie_variables' => true,
	            'cache'=>true
	        ),
	        'memorize_headers' => array(
	        	'Location',
        	),
	        'regexps' => array(
				'^/$' => array('cache' => false),
       			'^/index/' => array('cache' => false),
	        	'^/auth/' => array('cache' => false),
	        	'^/auth/logout' => array('cache' => false),
			)
    	);
 
        $backendOptions  = array(        	
            'cache_dir' => realpath(APPLICATION_PATH . '/../data/cache')
        );
        
        $cache = Zend_Cache::factory('Page',
                                     'File',
                                     $frontendOptions,
                                     $backendOptions);
                                     
 		Zend_Registry::set('cache', $cache);
 		//$cache = Zend_Registry::get('cache');
        //$cache->start();
    }
}