<?php

require_once 'Zend/Http/Client.php';
require_once 'Zend/Http/Response.php';

class Koldunschik_Page_Info
{	
	var $body;
	
	function __construct($page){
 
	    $client = new Zend_Http_Client($page, array(
	        'maxredirects' => 0,
	        'timeout'      => 30));
	    
	    $response = Zend_Http_Response::fromString($client->request());
		
		if ($response->getStatus() != 200) {		
			throw new Exception('Error page status');		
		}
	    
	    $this->body = $response->getBody();
		
		//print_r( $response->getHeaders());
	    
		if ( preg_match("|charset=(.*)|i", $response->getHeader('Content-type'), $temp) ){
				
			$charset_heder = trim ($temp[1]);
			//echo $charset_heder;
			
		    if(strtoupper($charset_heder) != "utf-8"){
		    	$this->body = iconv(strtoupper($charset_heder), "utf-8", $this->body);
		    }
		}else if(preg_match('/<meta(?!\s*(?:name|value)\s*=)[^>]*?charset\s*=[\s"\']*([^\s"\'\/>]*)/i',  $this->body, $temp)){
			$charset_heder = trim ($temp[1]);
			//echo $charset_heder;
			
		    if(strtoupper($charset_heder) != "utf-8"){
		    	$this->body = iconv(strtoupper($charset_heder), "utf-8", $this->body);
		    }					
		}		
	}
	
	function __destruct(){

	}	
	
	function getTitle(){
		
		$title = " ";
		
		if(preg_match("|<\s*title\s*>(.*?)<\s*/title\s*>|is", $this->body, $tmp)){
			$title = $tmp[1];		
		}else{
			throw new Exception('Title not found');	
		}
						
		//trim(preg_replace('"|'.title($mainpage).'|"', "",  title($page)),"[#/\\-:\| ]+");
		
		return $title;	
	}
	
	
	function getKeywords(){
	
		$keywords = "";
		
		$string=$this->body;
	
		$search=array("'<script[^>]*?>.*?</script>'si",
			"'<style[^>]*?>.*?</style>'si",
			"'<[\/\!]*?[^<>]*?>'si", "'([\r\n])[\s]+'",
			"'&quot;'i", "'&amp;'i","'&lt;'i","'&gt;'i","'&nbsp;'i",
			"'&iexcl;'i","'&cent;'i","'&pound;'i","'&copy;'i","'&#(\d+);'e");
	
		$replace=array(' ',' ',' ',"\\1 ",'" ',' ',' ',' ',' ',
			chr(161),chr(162),chr(163),chr(169),"chr(\\1)");
		
	
		$string=preg_replace($search, $replace, $string);
		$string = preg_replace(array("|_|i", "|-|i", "|�|i", "|[0-9]+|i" ), ' ', $string);	
		$string = preg_replace('/\s\s+/i', ' ', $string); // replace whitespace
		$string = trim($string); // trim the string
		$string = preg_replace('/[^\p{L} ]/', '', $string); // only take alphanumerical characters, but keep the spaces and dashes too�
		$string = strtolower($string); // make it lowercase	
		
		preg_match_all('/[^\p{L}]+.*?[^\p{L}]+/i', $string, $matchWords);
		$matchWords = $matchWords[0];
	
		foreach ( $matchWords as $key=>$item) {
			
			$item = trim($item);
	
			if ($item == '' || strlen($item) <= 3 ) {
				unset($matchWords[$key]);
			}
		}
		  
		$wordCountArr = array();
		
		if ( is_array($matchWords) ) {
			
			foreach ( $matchWords as $key => $val ) {
				
				$val = strtolower(trim($val));
				
				if ( isset($wordCountArr[$val]) ) {
					$wordCountArr[$val]++;
				} else {
					$wordCountArr[$val] = 1;
				}
			}
		}
		
		arsort($wordCountArr);
		
		$wordCountArr = array_slice($wordCountArr, 0, 10);
		
		$keywords = implode(", ", array_keys($wordCountArr));
		
		return $keywords;
		
	}	
}
