<?php

class Application_Model_DbTable_Query extends Zend_Db_Table_Abstract
{
    protected $_name = 'query';
    //protected $_primary = array('idquery', 'pageid');
    protected $_primary = array('idquery');
    
    protected $_dependentTables = array('Application_Model_DbTable_Link', 'Application_Model_DbTable_Position');
    
    protected $_referenceMap = array(
        'refPage' => array(
            self::COLUMNS => 'pageid',
            self::REF_TABLE_CLASS => 'Application_Model_DbTable_Page',
            self::REF_COLUMNS => 'idpage',
            self::ON_DELETE => self::CASCADE,
            self::ON_UPDATE => self::CASCADE
        ),
        'refSite' => array(
            self::COLUMNS => 'siteid',
            self::REF_TABLE_CLASS => 'Application_Model_DbTable_Site',
            self::REF_COLUMNS => 'idsite',
            self::ON_DELETE => self::CASCADE,
            self::ON_UPDATE => self::CASCADE
        )
    );

    public function getQueries($siteid = 0, $filterField=0, $filterValue=0, $sortField=0, $sortOrder=0)
    {
    	
        $this->_db->setFetchMode(Zend_Db::FETCH_OBJ);	

        $firstQuery = $this->_db->select()
			  ->from('position',
			         array('number', 'queryid'))
			  ->group('queryid');
		
		$query =  $this->_db->select()
						->from('query',
								array('*'))						 					 
						 ->join('page',
	                   			'query.pageid=page.idpage',
						 		 array('relative'))
						 ->join('site',
	                   			'page.siteid=site.idsite',
						 		 array('host'))
						 ->join(array('position' => $firstQuery),
	                   	 		'query.idquery=position.queryid',
						 		 array('number'=>'IFNULL( number, 0 )'));
						 		 
						  /*		 
						 ->joinLeft(array('view' => $secondQuery),
	                   			'query.idquery=view.queryid',
						 		 array('views'=>'IFNULL( views, 0 )'));						 		 
						
						 ->joinLeft(array('view' => $secondQuery),
	                   			'page.idpage=view.pageid',
						 		 array('views'));
						 */
	
    	if( $siteid ){			
			$query->where('site.idsite = ?', $siteid);
		}
		
    	if( $filterField AND $filterValue){
    		//print_r($filterField);
			$query->where($filterField . ' = ?', $filterValue);
		}
		
    	if( $sortField AND $sortOrder ){
			$query->order($sortField . ' ' . $sortOrder);
		}
		
		//echo $query->assemble();
		 
		$row = $this->_db->fetchAll( $query );
		
		
		/*
    	if (!$row) {
            throw new Exception("Could not find row");
        }
        */
        
        return $row;
    }
    
	public function getPaginator($siteid = 0, $filterField=0, $filterValue=0, $sortField=0, $sortOrder=0)
    {
    	
        $this->_db->setFetchMode(Zend_Db::FETCH_OBJ);	

        $firstQuery = $this->_db->select()
			  ->from('position',
			         array('maxnumber'=>'MAX(number)'))
			  ->where('query.idquery=position.queryid');
			  
		$secondQuery = $this->_db->select()
                //->setIntegrityCheck(false)
                ->from('position',
                		array('number'))
                ->where('query.idquery=position.queryid')
                ->order('position.date DESC')
                ->limit(1);
		
		$query =  $this->_db->select()
						->from('query',
								array('*',
								'maxnumber' => new Zend_Db_Expr('(' . $firstQuery . ')'),
								'number' => new Zend_Db_Expr('(' . $secondQuery . ')'),
								)) 
						 ->join('page',
	                   			'query.pageid=page.idpage',
						 		 array('relative'))
						 ->join('site',
	                   			'page.siteid=site.idsite',
						 		 array('host'));
						 /*
						 ->join(array('position' => $firstQuery),
	                   	 		'query.idquery=position.queryid',
						 		 array('number', 'maxnumber'));
						 		 
						 		 
						 ->joinLeft(array('view' => $secondQuery),
	                   			'query.idquery=view.queryid',
						 		 array('views'=>'IFNULL( views, 0 )'));						 		 
						
						 ->joinLeft(array('view' => $secondQuery),
	                   			'page.idpage=view.pageid',
						 		 array('views'));
						 */
	
    	if( $siteid ){			
			$query->where('site.idsite = ?', $siteid);
		}
		
    	if( $filterField AND $filterValue){
    		//print_r($filterField);
			$query->where($filterField . ' = ?', $filterValue);
		}
		
    	if( $sortField AND $sortOrder ){
			$query->order($sortField . ' ' . $sortOrder);
		}
		
		//echo $query->assemble();
		
		return $query;
    }
    
    
    public function getQuery($id)
    {
        $id = (int)$id;
        $row = $this->fetchRow('idquery = ' . $id);
        if (!$row) {
            throw new Exception("Could not find row $id");
        }
        return $row;
    }

	public function getStatuses($siteid)
    {
        $this->_db->setFetchMode(Zend_Db::FETCH_OBJ);
		
		$query =  $this->_db->select()
						->from('query',
								array('status', 'number'=>'count(*)'))
						 ->join('page',
	                   			'query.pageid=page.idpage',
						 		 array())
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
    
	public function updateQuery($idquery, $status)
	{
        $data = array(
			'status' => $status
        );
        
        $this->update($data, 'idquery = '. (int) $idquery);
	}
	
	public function updateQueries($idquery, $status)
	{
        $data = array(
			'status' => $status
        );
        
        $this->update($data, $this->_db->quoteInto("idquery IN (?)", $idquery));
	}
	
	public function deleteQueries($idquery){
		
        $this->delete($this->_db->quoteInto("idquery IN (?)", $idquery));     	
    }
    
	public function deletePagesQueries($idpage){
		
		$sql = $this->_db->quoteInto("DELETE link FROM link
			JOIN query ON(query.idquery=link.queryid)
			WHERE query.pageid IN (?)", $idpage);
		
		$this->_db->query($sql);
		
		
		$sql = $this->_db->quoteInto("DELETE FROM position 			
			WHERE pageid IN (?)", $idpage);
		
		$this->_db->query($sql);
		
		/*
		$sql = $this->_db->quoteInto("DELETE link FROM link
			LEFT JOIN query ON(query.idquery=link.queryid)
			WHERE query.pageid IN (?)", $idpage);
		
		$this->_db->query($sql);
		*/
		
        $this->delete($this->_db->quoteInto("pageid IN (?)", $idpage));
    }    
}

