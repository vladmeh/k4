<?php 

require_once 'Zend/Registry.php';

class Koldunschik_Position
{
	var $db;	

	function __construct(){		
		$this->db = Zend_Registry::get('k_db');
	}
	
	function __destruct(){
		unset($this->db);
	}
	
	function addPosition($siteid, $pageid, $queryid, $number){
		
		$data = array(
			'siteid' => $siteid,
			'pageid' => $pageid,
			'queryid' => $queryid,
		    'number' => $number,
		    'date' => date('Ymd' , time())
		);
			 
		$this->db->insert('position', $data);
			
		return $this->db->lastInsertId();
	}
}