<?php 

require_once 'Zend/Registry.php';

class Koldunschik_View
{
	private $db;
	
	function __construct(){
		$this->db = Zend_Registry::get('k_db');
	}
	
	function __destruct(){
		unset($this->db);
	}
	
	/*
	function countQueryView($queryid){
				
		try{
			$this->db->setFetchMode(Zend_Db::FETCH_OBJ);
			
			$date = date('Ymd' , time()-(30*24*60*60) );
			
			$query =  $this->db->select()
						->from('view',
								array('count' => 'count(*)'))
						 ->where('queryid = ?', $queryid)
						 ->where('DATE(date) >= ?', $date);
			
			$query = $this->db->fetchRow( $query );
		
		}catch(Exception $ex){
			$query = null;
		}

		return $query->count;
	}
	*/	
	
	function countPageView($pageid){
				
		try{
			$this->db->setFetchMode(Zend_Db::FETCH_OBJ);
			
			$date = date('Ymd' , time()-(30*24*60*60) );
			
			$query =  $this->db->select()
						->from('view',
								array('count' => 'count(*)'))
						 ->where('pageid = ?', $pageid)
						 ->where('DATE(date) >= ?', $date);
			
			$query = $this->db->fetchRow( $query );
		
		}catch(Exception $ex){
			$query = null;
		}

		return $query->count;
	}
	


	function addView($siteid, $pageid){
			
		$data = array(
			'siteid' => $siteid,
			'pageid' => $pageid,
		    'date' => date('Ymd' , time())
		);
		 
		$this->db->insert('view', $data);
		
		return $this->db->lastInsertId();
	}
	
}