<?php

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/application'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
	realpath(dirname(__FILE__) . '/library'),
	get_include_path(),
)));

/** Zend_Application */
require_once 'Zend/Application.php';

require_once 'Zend/Config.php';

require_once 'Zend/Db.php';

require_once 'Zend/Registry.php';

require_once 'Koldunschik/Link.php';

require_once 'Koldunschik/Query.php';

require_once 'Koldunschik/Bot.php';

require_once 'Koldunschik/Page/Info.php';

require_once 'Koldunschik/View.php';

require_once 'Koldunschik/Setting.php';


// Create application, bootstrap, and run
$k_application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);

$k_config = new Zend_Config($k_application->getOptions());

try{
	$k_db = Zend_Db::factory($k_config->resources->db);
	$k_db->getConnection();
} catch (Zend_Db_Adapter_Exception $e) {
	die('возможно, неправильные параметры соединения или СУРБД не запущена');
} catch (Zend_Exception $e) {
	die('возможно, попытка загрузки требуемого класса адаптера потерпела неудачу');
}

Zend_Registry::set('k_db', $k_db);

$k_bot = new Koldunschik_Bot();

$k_query = new Koldunschik_Query();

$k_page = new Koldunschik_Page();

$k_link = new Koldunschik_Link();

$k_view = new Koldunschik_View();

$k_setting = new Koldunschik_Setting();

if( $k_bot->userAgent()->isBot() ){

	try{
		//$k_page->getPageID($k_page->getHost(), $k_page->getRelative());

		$k_siteID = $k_page->getSiteID($k_page->getHost());

		$k_pageID = $k_page->getPageID($k_page->getHost(), $k_page->getRelative());

		$k_pageStatus = $k_page->getPageStatus($k_pageID);

		if($k_pageStatus != 'DEL'){
			$k_bot->addIndex($k_siteID, $k_pageID, $k_bot->getBot());
		}

	}catch(Exception $ex){
		//print_r($ex);
	}

} else if($k_query->referer()->isGoogleSearch()){

	try{

		$k_site_setting = $k_setting->getSetting($k_page->getHost());

		/*pr vladmeh if(!$k_site_setting->linking)*/
		if(empty($k_site_setting->linking)){
			throw new Exception('Linking not work');
		}

		$k_queryName = $k_query->referer()->getGoogleQuery();

		$k_query->setName($k_queryName);

		$k_query->setRank($k_query->referer()->getGoogleRank());

		$k_query->setHost($k_page->getHost());

		$k_query->setRelative($k_page->getRelative());

		$k_query->setSE('google');

		$k_pageID = $k_page->getPageID($k_page->getHost(),
										 $k_page->getRelative());

		//лишний запрос
		$k_pageStatus = $k_page->getPageStatus($k_pageID);


		if( $k_site_setting->googlerank < $k_query->googleRank
		    AND mb_strlen($k_query->googleQuery, 'utf8') <= $k_site_setting->maxquerylength
			AND !$k_query->isSymbol()
			AND $k_pageStatus != 'DEL' AND $k_pageStatus != 'LINK'
			){

				$k_siteID = $k_site_setting->siteid;

				if($k_link->countLink($k_page->getHost(), $k_pageID ) < $k_site_setting->maxquerylinks){

			 		if( ($k_site_setting->minpageview == 0 OR $k_view->countPageView($k_pageID)>=$k_site_setting->minpageview)
			 			AND $k_query->countQuery( $k_pageID, date('Ymd' , time())) < $k_site_setting->maxlinksday
			 			){

			 			$k_db->beginTransaction();

						$k_queryID = $k_query->addQuery($k_siteID, $k_pageID, $k_site_setting->querystatus);

						$k_position = new Koldunschik_Position();
						$k_position->addPosition($k_siteID, $k_pageID, $k_queryID, $k_query->googleRank);

						$k_db->commit();


						for($i=0; $i < $k_site_setting->numberlinks; $i++){
			 				$k_link->addLink($k_siteID, $k_page->getLastPage($k_siteID, $k_pageID), $k_queryID );
						}

			 		}

			 		$k_view->addView( $k_siteID, $k_pageID );

			 	} else if( $k_site_setting->multisite_linking
			 			  AND $k_link->countMultisiteLink($k_page->getHost(), $k_pageID ) < $k_site_setting->multisite_maxquerylinks ){


			 		$k_db->beginTransaction();

			 		$k_queryID = $k_query->addQuery($k_siteID, $k_pageID, $k_site_setting->querystatus);

			 		$k_position = new Koldunschik_Position();
					$k_position->addPosition($k_siteID, $k_pageID, $k_queryID, $k_query->googleRank);

					$k_db->commit();


					for($i=0; $i< $k_site_setting->numberlinks; $i++){
				 		$k_link->addLink($k_siteID, $k_page->getMultisiteRelatedPage($k_siteID, $k_pageID, $k_query->googleQuery) ,
				 			$k_queryID );
					}

			 	}

		}

	}catch(Exception $ex){
		//print_r($ex);
	}

} else if($k_query->referer()->isYandexSearch()){

	try{

		$k_site_setting = $k_setting->getSetting($k_page->getHost());

		/*pr vladmeh if(!$k_site_setting->linking)*/
		if(empty($k_site_setting->linking)){
			throw new Exception('Linking not work');
		}

		$k_queryName = $k_query->referer()->getYandexQuery();

		$k_query->setName( $k_queryName );

		$k_query->setRank( $k_query->referer()->getYandexRank() );

		$k_query->setHost($k_page->getHost());

		$k_query->setRelative($k_page->getRelative());

		$k_query->setSE('yandex');



		$k_pageID = $k_page->getPageID($k_page->getHost(),
										 $k_page->getRelative());

		$k_pageStatus = $k_page->getPageStatus($k_pageID);

		if( $k_site_setting->yandexrank < $k_query->yandexRank
			AND mb_strlen($k_query->yandexQuery, 'utf8') <= $k_site_setting->maxquerylength
			AND !$k_query->isSymbol()
			AND $k_pageStatus != 'DEL' AND $k_pageStatus != 'LINK'

			){
				$k_siteID = $k_site_setting->siteid;


				if($k_link->countLink($k_page->getHost(), $k_pageID ) < $k_site_setting->maxquerylinks){

			 		if( ($k_site_setting->minpageview == 0 OR $k_view->countPageView($k_pageID)>=$k_site_setting->minpageview)
			 			AND $k_query->countQuery( $k_pageID, date('Ymd' , time())) < $k_site_setting->maxlinksday){

			 			$k_db->beginTransaction();

			 			$k_queryID = $k_query->addQuery($k_siteID, $k_pageID, $k_site_setting->querystatus);

			 			$k_position = new Koldunschik_Position();
						$k_position->addPosition($k_siteID, $k_pageID, $k_queryID, $k_query->yandexRank);

						$k_db->commit();

			 			for($i=0; $i< $k_site_setting->numberlinks; $i++){
			 				$k_link->addLink($k_siteID, $k_page->getLastPage($k_siteID, $k_pageID), $k_queryID );
			 			}
			 		}


			 		$k_view->addView( $k_siteID, $k_pageID );

			 	} else if( $k_site_setting->multisite_linking
			 			  AND $k_link->countMultisiteLink($k_page->getHost(), $k_pageID ) < $k_site_setting->multisite_maxquerylinks){

			 		$k_db->beginTransaction();

			 		$k_queryID = $k_query->addQuery($k_siteID, $k_pageID, $k_site_setting->querystatus);

			 		$k_position = new Koldunschik_Position();
					$k_position->addPosition($k_siteID, $k_pageID, $k_queryID, $k_query->yandexRank);

					$k_db->commit();

			 		for($i=0; $i< $k_site_setting->numberlinks; $i++){
				 		$k_link->addLink($k_siteID,  $k_page->getMultisiteRelatedPage($k_siteID, $k_pageID, $k_query->yandexQuery),
				 			$k_queryID );
			 		}
			 	}
		}

	}catch(Exception $ex){
		//print_r($ex);
	}
}

$k_link->getLinks($k_page->getHost(), $k_page->getRelative());


$k_db->closeConnection();

