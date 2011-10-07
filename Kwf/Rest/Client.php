<?php

class Kwf_Rest_Client extends Zend_Rest_Client
{

    public function __construct($serviceUrl = null)
    {
        if (is_null($serviceUrl)) {
            $serviceUrl = Zend_Registry::get('config')->service->users->url;
            if (!$serviceUrl) {
                throw new Kwf_Exception(("'service.users.url' not defined in config (usually defined in KWF config)"));
            }
        }

        parent::__construct($serviceUrl);
    }

}