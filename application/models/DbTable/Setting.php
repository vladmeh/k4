<?php

class Application_Model_DbTable_Setting extends Zend_Db_Table_Abstract
{
    protected $_name = 'setting';
    //protected $_primary = array('idsetting', 'siteid');
    protected $_primary = array('idsetting');
    
    protected $_referenceMap = array(
        'refSite' => array(
            self::COLUMNS => 'siteid',
            self::REF_TABLE_CLASS => 'Application_Model_DbTable_Site',
            self::REF_COLUMNS => 'idsite',
            self::ON_DELETE => self::CASCADE,
            self::ON_UPDATE => self::CASCADE
        )
    );
    
    public function getSetting($siteid)
    {
        $siteid = (int) $siteid;
        $row = $this->fetchRow('siteid = ' . $siteid);
        
        if (!$row) {
            throw new Exception("Could not find row $siteid");
        }
        
        return $row;
    }
    
    
    public function updateSetting($idsetting, $siteid, $googlerank, $yandexrank,
					$maxlinksday, $minpageview, $maxquerylength, $linking,
					$maxquerylinks, $maxpagelinks, $numberlinks, $querystatus, /*$pagestatus,*/
					//$linklistclass, $linkitemclass,	$linknofound,
					$multisite_linking, $multisite_maxquerylinks)
	{

        $data = array(
			'siteid' => $siteid,
			'googlerank' => $googlerank,
			'yandexrank' => $yandexrank,
			'maxlinksday' => $maxlinksday,
			'minpageview' => $minpageview,
			'maxquerylength' => $maxquerylength,
			'linking' => $linking,
			'maxquerylinks' => $maxquerylinks,
			'maxpagelinks' => $maxpagelinks,
        	'numberlinks' => $numberlinks,
        	'querystatus' => $querystatus,
        	
        	//'pagestatus' => $pagestatus,
			//'linklistclass' => $linklistclass,
			//'linkitemclass' => $linkitemclass,
			//'linknofound' => $linknofound,
			'multisite_linking' => $multisite_linking,
			'multisite_maxquerylinks' => $multisite_maxquerylinks
        );
        
        $this->update($data, 'idsetting = '. (int) $idsetting);						
	}
	
	
	public function addSetting($siteid, $googlerank, $yandexrank,
					$maxlinksday, $minpageview, $maxquerylength, $linking,
					$maxquerylinks, $maxpagelinks, $numberlinks, $querystatus,
					//$linklistclass, $linkitemclass,	$linknofound,
					$multisite_linking, $multisite_maxquerylinks)
	{

        $data = array(
			'siteid' => $siteid,
			'googlerank' => $googlerank,
			'yandexrank' => $yandexrank,
			'maxlinksday' => $maxlinksday,
			'minpageview' => $minpageview,
			'maxquerylength' => $maxquerylength,
			'linking' => $linking,
			'maxquerylinks' => $maxquerylinks,
			'maxpagelinks' => $maxpagelinks,
        	'numberlinks' => $numberlinks,
        	'querystatus' => $querystatus,
			//'linklistclass' => $linklistclass,
			//'linkitemclass' => $linkitemclass,
			//'linknofound' => $linknofound,
			'multisite_linking' => $multisite_linking,
			'multisite_maxquerylinks' => $multisite_maxquerylinks
        );
        
        $this->db->insert('setting', $data);
			
		return $this->db->lastInsertId();
	}
	
	
    
}

