<?php

class Application_Model_DbTable_User extends Zend_Db_Table_Abstract
{
    protected $_name = 'user';
    
    public function getUser($id)
    {
        $id = (int)$id;
        $row = $this->fetchRow('iduser = ' . $id);
        if (!$row) {
            throw new Exception("Could not find row $id");
        }
        return $row;
    }
    
	public function updateUser($iduser, $username, $password, $role)
	{
        $data = array(
        	'username' => $username,
        	'password' => $password,
        	'role' => $role
        );
        
        $this->update($data, 'iduser = '. (int) $iduser);
	}   
    
    public function addUser($username, $password, $role)
    {	
        $data = array(
        	'username' => $username,
        	'password' => $password,
        	'role' => $role
        );        
                      
        $this->insert($data);        
        
        return $this->getAdapter()->lastInsertId();      
    }
    
    
    
}

