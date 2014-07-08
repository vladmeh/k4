<?php

class Application_Model_DbTable_Page extends Zend_Db_Table_Abstract
{
    protected $_name = 'page';
    //protected $_primary = array('idpage', 'siteid');
    protected $_primary = array('idpage');
    
    protected $_dependentTables = array('Application_Model_DbTable_Link',
    	'Application_Model_DbTable_Position', 'Application_Model_DbTable_Query',
    	'Application_Model_DbTable_View', 'Application_Model_DbTable_Index');
    
    protected $_referenceMap = array(
        'refSite' => array(
            self::COLUMNS => 'siteid',
            self::REF_TABLE_CLASS => 'Application_Model_DbTable_Site',
            self::REF_COLUMNS => 'idsite',
            self::ON_DELETE => self::CASCADE,
            self::ON_UPDATE => self::CASCADE
        )
    );
    
	public function getPages($siteid=0, $filterField=0, $filterValue=0, $sortField=0, $sortOrder=0)
    {        
        $this->_db->setFetchMode(Zend_Db::FETCH_OBJ);
        
        $firstQuery = $this->_db->select()
			  ->from('view',
			         array('views'=>'count(*)', 'pageid'))			       
			  ->where('date > DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)')
			  ->group('pageid');
        
		$query =  $this->_db->select()
						->from('page',
								array('*'))						 
						 ->join('site',
	                   			'page.siteid=site.idsite',
						 		 array('host'))
						 ->joinLeft('link',
	                   			'page.idpage=link.pageid',
						 		 array('links' => 'count(idlink)'))
						 ->joinLeft(array('view' => $firstQuery),
	                   	 		'page.idpage=view.pageid',
						 		 array('views'=>'IFNULL( views, 0 )'))
						 ->group('page.idpage');
						 
    	if( $siteid ){
			//echo 'transport';			
			$query->where('site.idsite = ?', $siteid);
		}
		
    	if( $filterField AND $filterValue){
    		//print_r($filterField);
			$query->where($filterField . ' = ?', $filterValue);
		}
		
    	if( $sortField AND $sortOrder ){
			$query->order($sortField . ' ' . $sortOrder);
		}
		
		//echo $query->__toString();
		 
		$row = $this->_db->fetchAll( $query );
		
    	if (!$row) {
            //throw new Exception("Could not find row");
        }
        
        return $row;
    }
    
	public function getPaginator($siteid=0, $filterField=0, $filterValue=0, $sortField=0, $sortOrder=0)
    {        
        $this->_db->setFetchMode(Zend_Db::FETCH_OBJ);
        /*
        $firstQuery = $this->_db->select()
                //->setIntegrityCheck(false)
                ->from('link',
                		array('count(1)'))
                ->where('link.pageid = page.idpage');
		*/
        $secondQuery = $this->_db->select()
                //->setIntegrityCheck(false)
                ->from('view',
                		array('count(*)'))
                ->where('date >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)')   
                ->where('page.idpage = view.pageid');       
        
        $query = $this->_db->select()
        				//->setIntegrityCheck(false)
						->from('page',
								array('page.*',
								//'links' => new Zend_Db_Expr('(' . $firstQuery . ')'),
								'views' => new Zend_Db_Expr('(' . $secondQuery . ')')))						 
						 ->join('site',
	                   			'page.siteid=site.idsite',
						 		 array('host'));
						 
    	if( $siteid ){
			//echo 'transport';			
			$query->where('site.idsite = ?', $siteid);
		}
		
    	if( $filterField AND $filterValue){
    		//print_r($filterField);
			$query->where($filterField . ' = ?', $filterValue);
		}
		
    	if( $sortField AND $sortOrder ){
			$query->order($sortField . ' ' . $sortOrder);
		}
                
        return $query;
    }
    

    public function getPage($id)
    {
        $id = (int)$id;
        $row = $this->fetchRow('idpage = ' . $id);
        if (!$row) {
            throw new Exception("Could not find row $id");
        }
        return $row;
    }
    
	public function getStatuses($siteid)
    {
        $this->_db->setFetchMode(Zend_Db::FETCH_OBJ);
		
		$query =  $this->_db->select()
						->from('page',
								array('status', 'number'=>'count(*)'))
						 ->join('site',
	                   			'page.siteid=site.idsite',
						 		 array())
						 ->group('status');
						 
    	if( $siteid ){
			//echo 'transport';			
			$query->where('site.idsite = ?', $siteid);
		}
						 		 
		$row = $this->_db->fetchAll( $query );		
		
		/*
    	if (!$row) {
            throw new Exception("Could not find row");
        }
        */
        
        return $row;
    }
   
    public function updatePage($idpage, $siteid, $relative, $title, $status)
    {    	
    	$data = array(
            'siteid' => $siteid,
    		'relative' => $relative,
    		'title' => $title,
    		'status' => $status
        );
        
        $this->update($data, 'idpage = '. (int) $idpage);
        //return $this->getAdapter()->lastInsertId();      
    }
    
    public function updatePages($idpage, $status)
    {

        $data = array(
			'status' => $status
        );        
        //$idquery=array(19);
        
        $this->update($data, $this->_db->quoteInto("idpage IN (?)", $idpage)); 
    }
    
	public function updateLinksCounterPerPages($idpage)
    {
    	
    	$this->_db->query($this->_db->quoteInto("UPDATE page
			SET links = (SELECT count(*) FROM link WHERE page.idpage = link.pageid )
			WHERE page.idpage IN (?)", $idpage));
    }
    
}

