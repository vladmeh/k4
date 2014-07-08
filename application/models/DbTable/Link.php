<?php

class Application_Model_DbTable_Link extends Zend_Db_Table_Abstract
{
    protected $_name = 'link';
    //protected $_primary = array('idlink', 'queryid', 'pageid');
    protected $_primary = array('idlink');
    
    protected $_referenceMap = array(
        'refQuery' => array(
            self::COLUMNS => 'queryid',
            self::REF_TABLE_CLASS => 'Application_Model_DbTable_Query',
            self::REF_COLUMNS => 'idquery',
            self::ON_DELETE => self::CASCADE,
            self::ON_UPDATE => self::CASCADE
        ),
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
    
	public function getLinks($siteid = 0, $sortField=0, $sortOrder=0)
    {
        $this->_db->setFetchMode(Zend_Db::FETCH_OBJ);

        $firstQuery = $this->_db->select()
                ->from('index',
                		array(new Zend_Db_Expr('index.idindex IS NOT NULL')))
                ->where('index.pageid = page.idpage')
                ->where('index.date > link.date')
                ->where('index.se = query.se')
                //->order('index.date')
                ->limit(1);
		
		$query =  $this->_db->select()
						->from('link',
								array('*',
								'index' => new Zend_Db_Expr('(' . $firstQuery . ')')
								))						 
						 ->join('page',
	                   			'link.pageid=page.idpage',
						 		 array('relative'))
						 ->join('site',
	                   			'page.siteid=site.idsite',
						 		 array('host'))
						 ->join('query',
	                   			'link.queryid=query.idquery',
						 		 array('name'))
						 ->join(array('page2' => 'page'),
	                   			'query.pageid=page2.idpage',
						 		 array('relative2'=> 'relative'))
						 ->join(array('site2' => 'site'),
	                   			'page2.siteid=site2.idsite',
						 		 array('host2' => 'host'));						 		 
	
    	if( $siteid ){
			//echo 'transport';			
			$query->where('site.idsite = ?', $siteid);
		}
		
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
    
	public function getPaginator($siteid = 0, $sortField=0, $sortOrder=0)
    {
        $this->_db->setFetchMode(Zend_Db::FETCH_OBJ);

         $firstQuery = $this->_db->select()
                ->from('index',
                		array(new Zend_Db_Expr('index.idindex IS NOT NULL')))
                ->where('index.pageid = page.idpage')
                ->where('index.date > link.date')
                ->where('index.se = query.se')
                //->order('index.date')
                ->limit(1);
		
		$query =  $this->_db->select()
						->from('link',
								array('*',
								'index' => new Zend_Db_Expr('(' . $firstQuery . ')')
								))						 
						 ->join('page',
	                   			'link.pageid=page.idpage',
						 		 array('relative'))
						 ->join('site',
	                   			'page.siteid=site.idsite',
						 		 array('host'))
						 ->join('query',
	                   			'link.queryid=query.idquery',
						 		 array('name', 'se'))
						 ->join(array('page2' => 'page'),
	                   			'query.pageid=page2.idpage',
						 		 array('relative2'=> 'relative'))
						 ->join(array('site2' => 'site'),
	                   			'page2.siteid=site2.idsite',
						 		 array('host2' => 'host'));						 		 
	
    	if( $siteid ){
			//echo 'transport';			
			$query->where('site.idsite = ?', $siteid);
		}
		
    	if( $sortField AND $sortOrder ){
			$query->order($sortField . ' ' . $sortOrder);
		}
        
        return $query;
    }
    
    
    public function getLinkForIndexation($siteid=0){
    	$this->_db->setFetchMode(Zend_Db::FETCH_OBJ);		
		
		$query =  $this->_db->select()
						->from('link',
								array('*', 'date'=>'MAX(date)'))						 
						 ->join('page',
	                   			'link.pageid=page.idpage',
						 		 array('relative', 'title', 'idpage'))
						 ->join('site',
	                   			'page.siteid=site.idsite',
						 		 array('host'))
						 ->join('setting',
	                   			'setting.siteid=site.idsite',
						 		 array('maxpagelinks'))
						 ->join('query',
	                   			'link.queryid=query.idquery',
						 		 array('name'))
						 ->group('idpage')
						 ->having('count(idpage) >= maxpagelinks')
						 ->order('date DESC');						 		 
	
    	if( $siteid ){	
			$query->where('site.idsite = ?', $siteid);
		}
		
		//echo $query->__toString();
    	$query->limit(20);
		 
		$row = $this->_db->fetchAll( $query );
		
		/*
    	if (!$row) {
            throw new Exception("Could not find row");
        }
        */
        
        return $row;    	
    }

    public function getLink($id)
    {
    	$id = (int)$id;
        
    	$this->_db->setFetchMode(Zend_Db::FETCH_OBJ);		
		
		$query =  $this->_db->select()
						->from('link',
								array('*'))						 
						 ->join('page',
	                   			'link.pageid=page.idpage',
						 		 array('relative'))
						 ->join('site',
	                   			'page.siteid=site.idsite',
						 		 array('host'))
						 ->join('query',
	                   			'link.queryid=query.idquery',
						 		 array('name'))
						 ->where('link.idlink = ?', $id);
        
        $row = $this->_db->fetchRow( $query );
        
        if (!$row) {
            throw new Exception("Could not find row $id");
        }
        
        return $row;
    }

    public function deleteLinks($idlink){
    	
    	//конвертим idlink в pageid
    	//$this->_db->setFetchMode(Zend_Db::FETCH_OBJ);		
		
		$query =  $this->_db->select()
						->from('link',
								array('DISTINCT(link.pageid)'))
						 ->where('link.idlink IN (?)', $idlink);

		$idpage = $this->_db->fetchAll( $query );
		    	
    	//удаляем ссылки
        $this->delete($this->_db->quoteInto("idlink IN (?)", $idlink));
        
        if ($idpage) {
       		//обновляем счетчик
        	$dbTable_page = new Application_Model_DbTable_Page(); 
        	$dbTable_page->updateLinksCounterPerPages($idpage);   
        }     
    }
    
	public function deleteLinksPerPages($idpage){
		//удаляем ссылки
        $this->delete($this->_db->quoteInto("pageid IN (?)", $idpage));
		
        //обновляем счетчик ссылок
        $dbTable_page = new Application_Model_DbTable_Page(); 
        $dbTable_page->updateLinksCounterPerPages($idpage);

        /*
        $data = array(
			'links'      => 0,
		);
        
        $this->_db->update('page', $data, $this->_db->quoteInto("idpage IN (?)", $idpage));
        */
    }
    
	public function deletePromoLinks($idpage){

		$sql = $this->_db->quoteInto("DELETE link FROM link
			JOIN query ON(query.idquery=link.queryid)
			WHERE query.pageid IN (?)", $idpage);
		
		$this->_db->query($sql);
						 
        //$this->delete("queryid IN (?)", $row);
    }
    
	public function deleteLinksOnQueries($idquery){
		
		$query =  $this->_db->select()
						->from('query',
								array('DISTINCT(link.pageid)'))
						 ->join('link',
	                   			'query.idquery=link.queryid',
						 		 array('pageid'))
						 ->where('query.idquery IN (?)', $idquery);

		$idpage = $this->_db->fetchAll( $query );		
						
        $this->delete($this->_db->quoteInto("queryid IN (?)", $idquery));
        
        if ($idpage) {
        	$dbTable_page = new Application_Model_DbTable_Page(); 
        	$dbTable_page->updateLinksCounterPerPages($idpage);
        }
    }
}

