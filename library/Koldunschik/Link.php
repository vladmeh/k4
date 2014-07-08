<?php 

require_once 'Zend/Registry.php';

require_once 'Koldunschik/Page.php';

class Koldunschik_Link
{
	var $db;
	
	var $page;
	
	var $links = array();
	
	function __construct(){		
		$this->db = Zend_Registry::get('k_db');
		
		$this->page = new Koldunschik_Page();
	}
	
	function __destruct(){
		unset($this->db);	
		unset($this->page);	
	}
	
	/*
	function countLink($host, $pageid){
		
		$this->db->setFetchMode(Zend_Db::FETCH_OBJ);		
		
		$query =  $this->db->select()
							->from('link',
									array('count' => 'count(*)'))
							->join('query',
                   					'link.queryid = query.idquery',
							 		 array())
							->where('link.pageid = ?', $pageid);
		
		 
		$query = $this->db->fetchRow( $query );
		
		return $query->count;
	}
	function countLink($host, $pageid){
		
		$this->db->setFetchMode(Zend_Db::FETCH_OBJ);		
		
		$query =  $this->db->select()
							->from('link',
									array('count' => 'count(*)'))
							->join('query',
                   					'link.queryid = query.idquery',
							 		 array())
							->join(array('p1'=>'page'),
                   					'query.pageid = p1.idpage',
							 		 array())
							->join(array('p2'=>'page'),
                   					'link.pageid = p2.idpage',
							 		 array())		 
							->join('site',
                   					'p2.siteid = site.idsite',
							 		 array()) 		 		 
							->where('p1.idpage = ?', $pageid)
							->where('site.host = ?', $host);		
							
		$query = $this->db->fetchRow( $query );
		
		return $query->count;
	}
	*/	
	
	function countLink($host, $pageid){
		
		$this->db->setFetchMode(Zend_Db::FETCH_OBJ);		
		
		$query =  $this->db->select()
							->from('link',
									array('count' => 'count(*)'))
							->join('query',
                   					'link.queryid = query.idquery',
							 		 array())		 
							->join('site',
                   					'link.siteid = site.idsite',
							 		 array()) 		 		 
							->where('query.pageid = ?', $pageid)
							->where('site.host = ?', $host);		
							
		$query = $this->db->fetchRow( $query );
		
		return $query->count;
	}
	
	
	function countMultisiteLink($host, $pageid){
		
		$this->db->setFetchMode(Zend_Db::FETCH_OBJ);		
		
		$query =  $this->db->select()
							->from('link',
									array('count' => 'count(*)'))
							->join('query',
                   					'link.queryid = query.idquery',
							 		 array())
							->join('site',
                   					'link.siteid = site.idsite',
							 		 array()) 		 		 
							->where('query.pageid = ?', $pageid)
							->where('site.host != ?', $host);		
		
		$query = $this->db->fetchRow( $query );
		
		return $query->count;
	}
	
	function addLink($siteid, $pageid, $queryid){
		
		$data = array(
			'links'      => new Zend_Db_Expr('links+1'),
		);
		
		$this->db->update('page', $data, 'idpage = '.$pageid);
			
		$data = array(
			'siteid'      => $siteid,
		    'pageid'      => $pageid,			
		    'queryid' => $queryid,
			'date' => date('Ymd' , time())
		);
		 
		$this->db->insert('link', $data);		
		
		return $this->db->lastInsertId();
	}
	
	/*
	function getLinks($host, $relative){
		
		try{
		
			$this->db->setFetchMode(Zend_Db::FETCH_OBJ);
			
			$query =  $this->db->select()
						->from('site',
								array('site.host', 'p2.relative', 'q.name'))
						 ->join(array('p1' => 'page'),
	           					'site.idsite = page.siteid',
						 		 array('p1.relative', 'p1.idpage'))
						 ->join(array('l' => 'link'),
	           					'p1.idpage = link.pageid',
						 		 array('l.queryid'))
						 >join(array('q' => 'query'),
	                   			'link.queryid = q.idquery',
						 		 array('q.name', 'qpageid'=>'q.pageid'))
						 ->join(array('p2' => 'page'),
	                   			'qpageid=p2.idpage',
						 		 array('p2.relative'))
						 ->where('site.host = ?', $host)
						 ->where('p1.relative = ?', $relative)
						 ->limit(K_MAXPAGELINKS);
			
						
			$query =  $this->db->select()
						->from('link',
								array('site.host', 'page.relative', 'query.name'))
						 ->join('query',
	                   			'link.queryid = query.idquery',
						 		 array())
						 ->join('page',
	                   			'query.pageid=page.idpage',
						 		 array())	
						 ->where('site.host = ?', $host )
						 ->where('link.pageid = ?', $this->page->getExistingPageID($host, $relative) )
						 ->limit(K_MAXPAGELINKS);
						 
			$this->links = $this->db->fetchAll( $query );
			
			//$this->db->
			
			//print_r($this->links);
		
		}catch(Exception $ex){
			$this->links = null;
			//throw new Exception('Links not found');
		}

		return $this->links;
	}
	
		function getLinks(){
		
		try{
		
			$this->db->setFetchMode(Zend_Db::FETCH_OBJ);			
	
			$query = $this->db->select()
						->from('link',
								array())
						 ->joinLeft(array('p'=>'page'),
	                   			'link.pageid=p.idpage',
						 		 array())
						 ->joinLeft(array('s'=>'site'),
	                   			'p.siteid=s.idsite',
						 		 array())
						 ->joinLeft('query',
	                   			'link.queryid = query.idquery',
						 		 array('query.name'))
						 ->joinLeft('page',
	                   			'query.pageid=page.idpage',
						 		 array('page.relative'))
						 ->joinLeft('site',
	                   			'page.siteid=site.idsite',
						 		 array('site.host'))
						 ->where('s.host = ?', $this->page->getHost())
						 ->where('p.relative = ?', $this->page->getRelative())
						 ->where('query.status=?', 'OK');
						 //->where('page.status IN(?)', array('OK', 'LINK'))
						 //->limit(5);

			echo $query->__toString();
						 
			$this->links = $this->db->fetchAll( $query );
			
			//print_r($this->links);
		
		}catch(Exception $ex){
			$this->links = null;
			//throw new Exception('Links not found');
		}

		return $this->links;
	}	
	*/
	
	
	function getLinks(){
		
		try{
		
			$this->db->setFetchMode(Zend_Db::FETCH_OBJ);			
	
			$query = $this->db->select()
						->from('link',
								array())
						 ->join(array('p'=>'page'),
	                   			'link.pageid=p.idpage',
						 		 array())
						 ->join(array('s'=>'site'),
	                   			'p.siteid=s.idsite',
						 		 array())
						 ->join('query',
	                   			'link.queryid = query.idquery',
						 		 array('query.name'))
						 ->join('page',
	                   			'query.pageid=page.idpage',
						 		 array('page.relative'))
						 ->join('site',
	                   			'page.siteid=site.idsite',
						 		 array('site.host'))
						 ->where('s.host = ?', $this->page->getHost())
						 ->where('p.relative = ?', $this->page->getRelative())
						 ->where('query.status IN(?)', array('OK', 'STOP'));
						 //->where('page.status IN(?)', array('OK', 'LINK'))
						 //->limit(5);

			//echo $query->__toString();
						 
			$this->links = $this->db->fetchAll( $query );
			
			//print_r($this->links);
		
		}catch(Exception $ex){
			$this->links = null;
			//throw new Exception('Links not found');
		}

		return $this->links;
	}
	
	function mb_ucfirst($str, $encoding='UTF-8'){
        $str = mb_strtoupper(mb_substr($str, 0, 1, $encoding), $encoding).
               mb_substr($str, 1, mb_strlen($str), $encoding);
        return $str;
    }
	
	function showLinks($number=0, $linkListClass = "list",
					 $linkItemClass="item", $linkNoFound="Not found" ){
			
		if(count($this->links)>0){
				
			$otherlink = array();
				
			$i=0;		
			
			$links = '<ul class="'. $linkListClass .'">';
			
			foreach($this->links as $q){
				if($i < $number or $number == 0){
					$links .= '<li class="'. $linkItemClass
					.'"><a href="http://'. $q->host . $q->relative .'">'. $this->mb_ucfirst($q->name) .'</a></li>';
					$i++;
				}else{
					$otherlink[] = $q;
				}
			}
			
			$links .= '</ul>';
			
			$this->links = $otherlink;
		
		} else {
			$links = $linkNoFound;
		}
		
		return $links;
	}

}