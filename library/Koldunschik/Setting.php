<?php 

require_once 'Zend/Registry.php';

class Koldunschik_Setting
{
	var $db;	

	function __construct(){
		$this->db = Zend_Registry::get('k_db');
	}
	
	function __destruct(){
		unset($this->db);
	}
	
	function getSetting($host){
				
		try{
			$this->db->setFetchMode(Zend_Db::FETCH_OBJ);
			
			$query =  $this->db->select()
						->from('setting',
								'*')
						->joinLeft('site',
                   			'setting.siteid = site.idsite',
							array())
						->where('site.host = ?', $host);
			
			$query = $this->db->fetchRow( $query );
		
		}catch(Exception $ex){
			$query = null;
		}
		
		return $query;
	}
	
	function addSetting($siteid){
			
		$data = array(
		    'siteid'      => $siteid,
		    'googlerank' => 10,
			'yandexrank' => 10,
			'maxlinksday' => 5,
			'minpageview' => 0,
			'minqueryview' => 0,
			'maxquerylength' => 50,
			'linking' => 1,			
			'maxquerylinks' => 50,
			'maxpagelinks' => 5,
			'numberlinks' => 1,
			'querystatus' => 'OK',
			//'pagestatus' => 'OK',
			'multisite_linking' => 0,
			'multisite_maxquerylinks' => 50
		);
		 
		$this->db->insert('setting', $data);
		
		return $this->db->lastInsertId();
	}
	
	
	
}