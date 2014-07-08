<?php

require_once 'Zend/Registry.php';

require_once 'Koldunschik/Page.php';

class Koldunschik_Bot
{
	private $db;
	
	public $userAgent = "";
	
	public $bot = "";
	
	function __construct(){
		$this->db = Zend_Registry::get('k_db');

	}
	
	function __destruct(){

	}
	
	function userAgent(){
		
		if(empty($_SERVER['HTTP_USER_AGENT'])){
			$this->userAgent = "Anonymous";
		}else{
			$this->userAgent = $_SERVER['HTTP_USER_AGENT'];
		}
		
		return $this;
	}
	
	function isBot(){
		
		if(strpos($this->userAgent, 'Googlebot/')>0){
						
			$this->bot = 'google';
			return true;
		}else if(strpos($this->userAgent, 'YandexBot/')>0){
			
			$this->bot = 'yandex';
			return true;
		}
		
		return false;
	}
	
	function getBot(){
		
		return $this->bot;				
	}
	
	function isYandexBot(){
		
		if(strpos($this->userAgent, 'YandexBot/')>0){
			return true;
		}
		
		return false;		
	}
	
	function isGoogleBot(){
		if(strpos($this->userAgent, 'Googlebot/')>0){
			return true;
		}
		
		return false;		
	}	
	
	function addIndex($siteid, $pageid, $bot){
				
		$data = array(
			'siteid' => $siteid,
		    'pageid' => $pageid,
		    'se' => $bot,
			'date' => date('Ymd' , time())
		);
		 
		$this->db->insert('index', $data);
		
		return $this->db->lastInsertId();
	}
}
