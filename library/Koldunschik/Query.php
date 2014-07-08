<?php

require_once 'Zend/Registry.php';

require_once 'Koldunschik/Position.php';

require_once 'Koldunschik/Page.php';

class Koldunschik_Query
{
	var $db;

	//var $site;

	var $page;

	var $name;

	var $host;

	var $relative;

	var $se;

	var $status;

	var $googleQuery;

	var $googleRank;

	var $yandexQuery;

	var $yandexRank;

	var $referer;

	function __construct(){

		$this->db = Zend_Registry::get('k_db');

		$this->page = new Koldunschik_Page();

		//$this->site = new Koldunschik_Site();
	}

	function __destruct(){
		unset($this->db);
		unset($this->page);
	}

	function setName($name){
		$this->name = $name;
		return $this;
	}

	function setHost($host){

		$this->host = $host;
		return $this;
	}

	function setRelative($relative){

		$this->relative = $relative;
		return $this;
	}

	function setRank($rank){

		$this->rank = $rank;
		return $this;
	}

	function setSE($se){

		$this->se = $se;
		return $this;
	}

	function referer(){

		$this->referer = empty($_SERVER['HTTP_REFERER'])?"":$_SERVER['HTTP_REFERER'];

		return $this;
	}

	function countQuery($pageid, $day){

		$this->db->setFetchMode(Zend_Db::FETCH_OBJ);

		$query =  $this->db->select()
							->from('link',
									array('count' => 'count(*)'))
							->join('query',
                   					'link.queryid = query.idquery',
							 		 array())
							->where('query.pageid = ?', $pageid)
							->where('link.date = ?', $day);

		$query = $this->db->fetchRow( $query );

		return $query->count;
	}

	function parseURLQuery($var){

		$var  = parse_url($var, PHP_URL_QUERY);

		if(empty($var)){
			throw new Exception('URL query not found');
		}


		$var  = html_entity_decode($var);
		$var  = explode('&', $var);
		$arr  = array();


		foreach($var as $val)
		{
			$x = explode('=', $val);
			/*pr vladmeh*/
			if(count($x) > 1){
				$arr[$x[0]] = $x[1];
			}
		}

		unset($val, $x, $var);

		return $arr;
	}

	function isGoogleSearch(){

		if( preg_match("|google\.[a-z]+|i", $this->referer) ){
			return true;
		}

		return false;
	}

	function getGoogleRank(){

		$query = $this->parseURLQuery($this->referer);

		/*pr vladmeh*/
		if(!empty($query["cd"])){
			$this->googleRank = $query["cd"];
		}

		if(empty($this->googleRank)){
			throw new Exception('Google rank empty');
		}

		return $this->googleRank;
	}

	function getGoogleQuery(){

		$query = $this->parseURLQuery($this->referer);

		/*pr vladmeh*/
		if(!empty($query["q"])){
			$this->googleQuery = ucfirst( trim( urldecode($query["q"]) ) );
		}

		if (empty($this->googleQuery)){
		    throw new Exception('Query is empty');
		}

		return $this->googleQuery;
	}

	function isYandexSearch(){

		if( preg_match("|yandex\.[a-z]+|i", $this->referer) ){
			return true;
		}

		return false;
	}

	function getYandexRank(){

		$query = $this->parseURLQuery($this->referer);

		if(!empty($query["p"])){
			$this->yandexRank = $query["p"]*10+10;
		}else{
			$this->yandexRank = 10;
		}

		return $this->yandexRank;
	}

	function getYandexQuery(){

		$query = $this->parseURLQuery($this->referer);

		/*pr vladmeh*/
		if(!empty($query["text"])){
			$this->yandexQuery =  ucfirst( trim( urldecode($query["text"]) ) );
		}

		if (empty($this->yandexQuery)){
		    throw new Exception('Query is empty');
		}

		return $this->yandexQuery;
	}

	function isSymbol(){
		return preg_match('|[!+@#$%^&*()<>?_/\\\"\':]+|i', $this->name);
	}

	function getQuery($name, $se, $pageId){

		try{
			$this->db->setFetchMode(Zend_Db::FETCH_OBJ);

			$query =  $this->db->select()
						->from('query',
								'*')
						 ->where('query.name = ?', $name)
						 ->where('query.se = ?', $se)
						 ->where('query.pageid = ?', $pageId);

			$query = $this->db->fetchRow( $query );

		}catch(Exception $ex){
			$query = null;
		}

		return $query;
	}

	/*
	function isStopWords(){

		$stopwords = explode(",", K_QUERYSTOWORDS);

		$count = count($stopwords);

		$query = strtolower($this->name);

		for($i=0; $i<$count; $i++){

			$stopwords[$i] = strtolower( trim($stopwords[$i]) );

			if(empty($stopwords[$i])){
				throw new Exception('Stopword is empty');
			}

			if( strpos($query, $stopwords[$i]) !== false ){
				return true;
			}
		}

		return false;
	}
	*/

	function addQuery($siteId, $pageId, $queryStatus){


		/*
		if($pageId->status == 'DEL' or $pageId->status == 'LINK'){
			throw new Exception('Query not add');
		}
		*/

		$query = $this->getQuery($this->name, $this->se, $pageId);

		if($query){

			$queryid = $query->idquery;

			if($query->status == 'DEL' or $query->status == 'STOP'){
				throw new Exception('Query status delete or stop');
			}

		} else {
			//если запроса не существует то добавить и получить его id
			$data = array(
				'siteid' => $siteId,
				'pageid' => $pageId,
			    'name' => $this->name,
				'se' => $this->se,
				'status' => $queryStatus
			);

			$this->db->insert('query', $data);

			$queryid = $this->db->lastInsertId();
		}

		return $queryid;
	}
}