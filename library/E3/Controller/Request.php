<?php
/** Zend_Controller_Request_Abstract */
require_once 'Zend/Controller/Request/Http.php';

class E3_Controller_Request extends Zend_Controller_Request_Http
{
    public function setPage(E3_Component_Abstract $page) 
    {
        if (!$page instanceof E3_Component_Abstract) {
            throw new E3_Component_Exception('E3_Controller_Request requires a E3_Component_Abstract-based page object');
        }
    } 

}
