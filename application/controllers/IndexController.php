<?php

class IndexController extends Zend_Controller_Action
{
    public function init()
    {
        /* Initialize action controller here */
 	
    }

    public function indexAction()
    {
    	
    	$this->_redirector = $this->_helper->getHelper('Redirector');

        $this->_redirector->setCode(301)
                          ->setExit(false)
                          ->setGotoSimple("login",
                                          "auth");
    }
}