<?php
class Vps_Http_Requestor
{
    public function request($url)
    {
        $client = new Zend_Http_Client($url, array('timeout'=>30));
        return new Vps_Http_Requestor_Response($client->request());
    }
}
