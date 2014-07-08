<?php

class Application_Model_DbTable_Position extends Zend_Db_Table_Abstract
{
    protected $_name = 'position';
    //protected $_primary = array('idposition', 'queryid');
    protected $_primary = array('idposition');
    
    protected $_referenceMap = array(
        'refQuery' => array(
            self::COLUMNS => 'queryid',
            self::REF_TABLE_CLASS => 'Application_Model_DbTable_Query',
            self::REF_COLUMNS => 'idquery',
            self::ON_DELETE => self::CASCADE,
            self::ON_UPDATE => self::CASCADE
        ),
        'refSite' => array(
            self::COLUMNS => 'siteid',
            self::REF_TABLE_CLASS => 'Application_Model_DbTable_Site',
            self::REF_COLUMNS => 'idsite',
            self::ON_DELETE => self::CASCADE,
            self::ON_UPDATE => self::CASCADE
        ),
        'refPage' => array(
            self::COLUMNS => 'pageid',
            self::REF_TABLE_CLASS => 'Application_Model_DbTable_Page',
            self::REF_COLUMNS => 'idpage',
            self::ON_DELETE => self::CASCADE,
            self::ON_UPDATE => self::CASCADE
        )
    );

    public function getPositions($queryid = 0)
    {        
        $this->_db->setFetchMode(Zend_Db::FETCH_OBJ);	

        /*
        $firstQuery = $this->_db->select()
			  ->from('link',
			         array('links'=>'count(*)', 'queryid', 'link.date'))
			  ->group('queryid');
		*/
        
		$query =  $this->_db->select()
						->from('position',
								array('date', 'number'))		 
						 ->join('query',
	                   			'position.queryid=query.idquery',
						 		 array('name', 'se'))
						 ->joinLeft('link',
	                   			'position.queryid=link.queryid AND position.date >= link.date',
						 		 array('links'=>'count(link.idlink)'))						 
						 ->group('idposition')
						 ->order('position.date DESC');
		
	
    	if( $queryid ){
			//echo 'transport';			
			$query->where('position.queryid = ?', $queryid);
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
    
    public function getPaginator($queryid = 0)
    {        
        $this->_db->setFetchMode(Zend_Db::FETCH_OBJ);
        
		$query =  $this->_db->select()
						->from('position',
								array('date', 'number'))		 
						 ->join('query',
	                   			'position.queryid=query.idquery',
						 		 array('name', 'se'))
						 ->joinLeft('link',
	                   			'position.queryid=link.queryid AND position.date >= link.date',
						 		 array('links'=>'count(link.idlink)'))						 
						 ->group('idposition')
						 ->order('position.date DESC');
		
	
    	if( $queryid ){
			//echo 'transport';			
			$query->where('position.queryid = ?', $queryid);
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
    
	public function deletePositionsOnQueries($queryid){
        $this->delete($this->_db->quoteInto("queryid IN (?)", $queryid));     	
    }
}

