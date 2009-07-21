<?php
class Vps_Http_Requestor
{
    public function request($url)
    {
        $client = new Zend_Http_Client($url);
        return new Vps_Http_Requestor_Response($client->request());
    }
}
