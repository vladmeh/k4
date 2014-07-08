<?php 

require_once 'Zend/Registry.php';

require_once 'Koldunschik/Page/Info.php';

class Koldunschik_Page
{
	private $db;
	
	var $title;
	
	var $setting;
	
	function __construct(){
		$this->db = Zend_Registry::get('k_db');
		
		$this->setting = new Koldunschik_Setting();
	}
	
	function __destruct(){
		unset($this->db);
	}
	
	function getRelative(){
		return $_SERVER['REQUEST_URI'];
	}
	
	function getHost(){
		return $_SERVER['HTTP_HOST'];
	}
	
	function getUrl(){
			
		$url = ''; 			
		$default_port = 80;		
			
		if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS']=='on')){
			
			$url .= 'https://';
			$default_port = 443;
			
		}else{
			$url .= 'http://';			
		}

		$url .= $_SERVER['HTTP_HOST'];
			
		if ($_SERVER['SERVER_PORT'] != $default_port) {		
			$url .= ':'.$_SERVER['SERVER_PORT'];			
		}
		
		$url .= $_SERVER['REQUEST_URI'];
			
		return $url;
	}
	
	function getPageInfo($page){
					
		$pageInfo = new Koldunschik_Page_Info($page);
		
		return $pageInfo;
	}
	
	/*
	function getPageTitle($page=null){

		if(empty($page)){
			$page=$this->getUrl();
		}
				
		$page_contents = file_get_contents($page);		
			
		if(preg_match("|<title>(.*?)</title>|", $page_contents, $title)){
		
		}else{
			throw new Exception('Title not found');	
		}
				
		//trim(preg_replace('"|'.title($mainpage).'|"', "",  title($page)),"[#/\\-:\| ]+");
		
		return $title[1];
	}
	*/
	
	function getSiteID($host){
		
		$this->db->setFetchMode(Zend_Db::FETCH_OBJ);
		
		$query =  $this->db->select()
					->from('site',
							'site.idsite')
					->where('site.host = ?', $host);
		
		 
		$query = $this->db->fetchRow( $query );
		
		if(isset( $query->idsite ) ){
			
			return $query->idsite;
			
		}else{
			
			$data = array(
				'host' => $host,
			);
			 
			$this->db->insert('site', $data);
			
			$siteid = $this->db->lastInsertId();
			
			$this->setting->addSetting($siteid);
						
			return $siteid;
		}
	}	
	
	
	function getPage($host, $relative){
				
		try{
			$this->db->setFetchMode(Zend_Db::FETCH_OBJ);
			
			$query =  $this->db->select()
						->from('site',
								'*')
						 ->join('page',
	           					'site.idsite = page.siteid',
						 		 array())
						 ->where('site.host = ?', $host)
						 ->where('page.relative = ?', $relative);
			
			$query = $this->db->fetchRow( $query );
		
		}catch(Exception $ex){
			$query = null;
		}
		
		if(isset($query->idpage)){
			return $query;
		}else{
			return $this->addPage();
		}
	}
	
	//$siteId, $pageId, $queryStatus, $pageStatus
	function getPageID($host, $relative){
				
		try{
			$this->db->setFetchMode(Zend_Db::FETCH_OBJ);
			
			$query =  $this->db->select()
						->from('site',
								'page.idpage')
						 ->join('page',
	           					'site.idsite = page.siteid',
						 		 array())
						 ->where('site.host = ?', $host)
						 ->where('page.relative = ?', $relative);
			
			$query = $this->db->fetchRow( $query );
		
		}catch(Exception $ex){
			$query = null;
		}
		
		if(isset($query->idpage)){
			return $query->idpage;
		}else{
			return $this->addPage();
		}
	}
	
	function getExistingPageID($host, $relative){
		
		try{
			$this->db->setFetchMode(Zend_Db::FETCH_OBJ);			
			
			$query =  $this->db->select()
						->from('site',
								'page.idpage')
						 ->join('page',
	           					'site.idsite = page.siteid',
						 		 array())
						 ->where('site.host = ?', $host)
						 ->where('page.relative = ?', $relative);
						 //->where('page.status IN(?)', array('OK', 'LINK'));					
			
			$query = $this->db->fetchRow( $query );	
		
		}catch(Exception $ex){			
			throw new Exception('Page not found');
		}
		
		if(!empty($query->idpage)){
			return $query->idpage;
		}
		
		return null;			
	}
	
	function getPageStatus($idpage){
		
		try{
			$this->db->setFetchMode(Zend_Db::FETCH_OBJ);			
			
			$query =  $this->db->select()
						->from('page',
								'status')
						 ->where('idpage = ?', $idpage);
			
			$query = $this->db->fetchRow( $query );	
		
		}catch(Exception $ex){
			throw new Exception('Page not found');
		}
		
		if(!empty($query->status)){
			return $query->status;
		}
		
		return null;
	}
	
	/*
	function getLastPage($host, $link_pageid=0){
		
		try{
			$this->db->setFetchMode(Zend_Db::FETCH_OBJ);			
			
			$firstQuery =  $this->db->select()
					->from('link',
							'link.pageid')
					->joinLeft('query',
                   			'page.idpage = link.pageid',
							array())
					->where('query.pageid = ?', $link_pageid);

			$query =  $this->db->select()
					->from('site',
							'page.idpage')
					->joinLeft('page',
                   			'site.idsite = page.siteid',
							 array())
					->joinLeft('setting',
                   			'site.idsite = setting.siteid',
							 array('maxpagelinks'))
					->joinLeft('link',
                   			'page.idpage = link.pageid',
							array())
					//->joinLeft('query',
                   	//		'query.idquery = link.queryid',
					//		array())
					->where('site.host = ?', $host)
					//->where('IFNULL(link.pageid, 0) != ?', $link_pageid)
					->where('IFNULL(page.idpage, 0) != ?', $link_pageid)
					->where('page.idpage NOT IN (?)', $firstQuery)
					//->where('IFNULL(query.pageid, 0) != ?', $link_pageid)
					->where('page.status IN(?)', array('OK', 'LINK'))
					->group('link.pageid')
					->having('count(link.pageid) < setting.maxpagelinks')		
					->order('page.idpage');
					
			//echo $query->__toString();
					
			$query = $this->db->fetchRow( $query );
		
			if(empty($query->idpage)){
				throw new Exception('Page not found');
			}
		
		}catch(Exception $ex){
			throw new Exception('Page not found');
		}
		
		return $query->idpage;
	}
	
	function getLastPage($siteId, $link_pageid){
		
		try{
			$this->db->setFetchMode(Zend_Db::FETCH_OBJ);

			$query =  $this->db->select()
					->from('page',
							'page.idpage')
					->join('setting',
                   			'page.siteid = setting.siteid',
							 array('maxpagelinks'))
					->where('page.siteid = ?', $siteId)
					->where('page.idpage != ?', $link_pageid)
					->where('page.status IN(?)', array('OK', 'LINK'))					
					->where('links < setting.maxpagelinks')
					->limit(1);
					
			//echo $query->__toString();
					
			$query = $this->db->fetchRow( $query );
		
			if(empty($query->idpage)){
				throw new Exception('Page not found');
			}
		
		}catch(Exception $ex){
			throw new Exception('Page not found');
		}
		
		return $query->idpage;
	}
	*/
	
	function getLastPage($siteId, $link_pageid){
		
		try{
			$this->db->setFetchMode(Zend_Db::FETCH_OBJ);
			
			$firstQuery = $this->db->select()
                ->from('link',
                		array('DISTINCT(link.pageid)'))
                ->join('query',
                   			'link.queryid = query.idquery',
							 array(''))
                ->where('query.pageid = ?', $link_pageid);
                //echo $firstQuery->__toString();
				//exit();
                
			$query = $this->db->select()
					->from('page',
							'page.idpage')
					->join('setting',
                   			'page.siteid = setting.siteid',
							 array(''))
					->where('page.siteid = ?', $siteId)
					->where('page.idpage != ?', $link_pageid)
					->where('page.status IN(?)', array('OK', 'NEW', 'LINK' ))					
					->where('links < setting.maxpagelinks')
					->where('page.idpage NOT IN(?)', $firstQuery)
					->limit(1);					
					
			//echo $query->__toString();
					
			$query = $this->db->fetchRow( $query );
		
			if(empty($query->idpage)){
				throw new Exception('Page not found');
			}
		
		}catch(Exception $ex){
			throw new Exception('Page not found');
		}
		
		return $query->idpage;
	}
	
	
	function getMultisiteRelatedPage($siteId, $link_pageid=0, $search_query){
		
		try{
			
			$this->db->setFetchMode(Zend_Db::FETCH_OBJ);
			
			$firstQuery = $this->db->select()
                ->from('link',
                		array('DISTINCT(link.pageid)'))
                ->join('query',
                   			'link.queryid = query.idquery',
							 array(''))
                ->where('query.pageid = ?', $link_pageid);
                //echo $firstQuery->__toString();
				//exit();
		
			$query =  $this->db->select()
					->from('site',
							array('page.idpage',
								'relevenceScore'=>$this->db->quoteInto('MATCH(title) AGAINST(?)', $search_query) ))
					->join('page',
                   			'site.idsite = page.siteid',
							 array())
					->join('setting',
                   			'page.siteid = setting.siteid',
							 array('maxpagelinks'))
					->where('page.siteid != ?', $siteId)
					->where('page.status IN(?)', array('OK', 'NEW', 'LINK'))
					->where('page.links < setting.maxpagelinks')
					->where('page.idpage NOT IN(?)', $firstQuery)
					->where('MATCH(title) AGAINST(?)', $search_query)
					->order('relevenceScore DESC')
					->limit(1);
			
			//echo $query->__toString();			
			$query = $this->db->fetchRow( $query );
			
			//print_r($query);`search` (`title` ASC, `keywords` ASC) ;
			
			if(empty($query->idpage)){
				throw new Exception('Related page not found');
			}
		
		}catch(Exception $ex){
			throw new Exception('Related page not found');
		}
		
		return $query->idpage;		
	}
	

	private function addPage(){
		/*
		if(!$pageStatus){
			$k_site_setting = $this->setting->getSetting($this->getHost());	
			$pageStatus = $k_site_setting->pagestatus;							
		}
		*/
				
		$pageInfo=$this->getPageInfo($this->getUrl());
				
		$data = array(
			'siteid' => $this->getSiteID( $this->getHost() ),
		    'title' => $pageInfo->getTitle(),
			//'keywords' => 'keywords',
			//'keywords' => $pageInfo->getKeywords(),
		    'relative' => $this->getRelative(),
			'status' => 'NEW'
		);
		
		unset($pageInfo);
		 
		$this->db->insert('page', $data);
		
		return $this->db->lastInsertId();
	}
	
}