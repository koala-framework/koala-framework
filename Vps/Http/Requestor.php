<?php
class Vps_Http_Requestor
{
    public static function getInstance()
    {
        static $i;
        if (!isset($i)) {
            if (class_exists('HttpRequest')) {
                $i = new Vps_Http_Pecl_Requestor();
            } else {
                $i = new self();
            }
        }
        return $i;
    }

    public function request($url)
    {
        $client = new Zend_Http_Client($url, array('timeout'=>30));
        return new Vps_Http_Requestor_Response($client->request());
    }
}
