<?php

class Vps_Rest_Client extends Zend_Rest_Client
{

    public function __construct($serviceUrl = null)
    {
        if (is_null($serviceUrl)) {
            $serviceUrl = Zend_Registry::get('config')->service->users->url;
            if (!$serviceUrl) {
                throw new Vps_Exception(("'service.users.url' not defined in config (usually defined in VPS config)"));
            }
        }

        parent::__construct($serviceUrl);
    }

}