<?php

class Application_Model_DbTable_View extends Zend_Db_Table_Abstract
{
    protected $_name = 'view';
    //protected $_primary = array('idview', 'pageid');
    protected $_primary = array('idview');
    
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
}

