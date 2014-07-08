<?php

class Application_Model_DbTable_Site extends Zend_Db_Table_Abstract
{
    protected $_name = 'site';
    protected $_primary = array('idsite');
    
    protected $_dependentTables = array( 
    	'Application_Model_DbTable_Link', 'Application_Model_DbTable_Position', 'Application_Model_DbTable_Query',     	
    	'Application_Model_DbTable_View', 'Application_Model_DbTable_Page', 'Application_Model_DbTable_Setting',
    	'Application_Model_DbTable_Index'
    );
    
    public function getSites($sortField=0, $sortOrder=0)
    {
        $this->_db->setFetchMode(Zend_Db::FETCH_OBJ);

        $firstQuery = $this->_db->select()
			  ->from('page',
			         array('pages'=>'count(*)', 'siteid'))
			  ->group('siteid');
			  
		$secondQuery = $this->_db->select()
			  ->from('link',
			         array('links'=>'count(*)'))
			  ->join('page',
			  		'link.pageid = page.idpage',
			  		array('siteid'))
			  ->group('siteid');
			  
		$thirdQuery = $this->_db->select()
			  ->from('query',
			         array('queries'=>'count(*)'))
			  ->join('page',
			  		'query.pageid = page.idpage',
			  		array('siteid', 'idpage'))
			  ->group('siteid');
	
		$query =  $this->_db->select()
						->from('site',
								array('*'))						 
						 ->joinLeft(array('page' => $firstQuery),
	                   			'site.idsite=page.siteid',
						 		 array('pages' => 'IFNULL( pages, 0 )'))
						 ->joinLeft(array('link' => $secondQuery),
	                   			'site.idsite=link.siteid',
						 		 array('links' => 'IFNULL( links, 0 )'))
						 ->joinLeft(array('query' => $thirdQuery),
	                   			'site.idsite=query.siteid',
						 		 array('queries' => 'IFNULL( queries, 0 )'));		 
						 		 
		//echo $query->assemble();
    	if( $sortField AND $sortOrder ){
			$query->order($sortField . ' ' . $sortOrder);
		}						 		 
		 
		$row = $this->_db->fetchAll( $query );
		/*
    	if (!$row) {
            throw new Exception("Could not find row");
        }
        */
        return $row;
    }
    
    public function getPaginator($sortField=0, $sortOrder=0)
    {
        $this->_db->setFetchMode(Zend_Db::FETCH_OBJ);

        $firstQuery = $this->_db->select()
			  ->from('page',
			         array('pages'=>'count(*)', 'siteid'))
			  ->group('siteid');
			  
		$secondQuery = $this->_db->select()
			  ->from('link',
			         array('links'=>'count(*)'))
			  ->join('page',
			  		'link.pageid = page.idpage',
			  		array('siteid'))
			  ->group('siteid');
			  
		$thirdQuery = $this->_db->select()
			  ->from('query',
			         array('queries'=>'count(*)'))
			  ->join('page',
			  		'query.pageid = page.idpage',
			  		array('siteid', 'idpage'))
			  ->group('siteid');
	
		$query =  $this->_db->select()
						->from('site',
								array('*'))						 
						 ->joinLeft(array('page' => $firstQuery),
	                   			'site.idsite=page.siteid',
						 		 array('pages' => 'IFNULL( pages, 0 )'))
						 ->joinLeft(array('link' => $secondQuery),
	                   			'site.idsite=link.siteid',
						 		 array('links' => 'IFNULL( links, 0 )'))
						 ->joinLeft(array('query' => $thirdQuery),
	                   			'site.idsite=query.siteid',
						 		 array('queries' => 'IFNULL( queries, 0 )'));		 
						 		 
		//echo $query->assemble();
    	if( $sortField AND $sortOrder ){
			$query->order($sortField . ' ' . $sortOrder);
		}						 		 
		 
        return $query;
    }
    
    
    public function getSite($id)
    {
        $id = (int)$id;
        $row = $this->fetchRow('idsite = ' . $id);
        if (!$row) {
            throw new Exception("Could not find row $id");
        }
        return $row;
    }
   
    public function updateSite($idsite, $host)
    {
    	
        $data = array(
        	'host' => $host      
        );
                              
        $this->update($data, 'idsite = '. (int) $idsite);
        
        return $this->getAdapter()->lastInsertId();      
    }
    
    
    
}

