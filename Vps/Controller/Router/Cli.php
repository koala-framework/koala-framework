<?php
class Vps_Controller_Router_Cli extends Zend_Controller_Router_Abstract
{
    public function route(Zend_Controller_Request_Abstract $request)
    {
        return $request;
    }
}
