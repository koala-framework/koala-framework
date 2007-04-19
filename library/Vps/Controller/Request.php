<?php
/** Zend_Controller_Request_Abstract */
require_once 'Zend/Controller/Request/Http.php';

class Vps_Controller_Request extends Zend_Controller_Request_Http
{
    public function setPage(Vps_Component_Abstract $page) 
    {
        if (!$page instanceof Vps_Component_Abstract) {
            throw new Vps_Component_Exception('Vps_Controller_Request requires a Vps_Component_Abstract-based page object');
        }
    } 

}
