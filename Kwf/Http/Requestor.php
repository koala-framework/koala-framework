<?php
class Vps_Http_Requestor
{
    private $_cache = array();

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

    public final function request($url)
    {
        if (array_key_exists($url, $this->_cache)) {
            return $this->_cache[$url];
        }
        $this->_cache[$url] = $this->_request($url);
        return $this->_cache[$url];
    }

    protected function _request($url)
    {
        $client = new Zend_Http_Client($url, array('timeout'=>30));
        return new Vps_Http_Requestor_Response($client->request());
    }

    public function clearCache()
    {
        $this->_cache = array();
    }

    public function cacheResponse($url, Vps_Http_Requestor_Response_Interface $response = null)
    {
        $this->_cache[$url] = $response;
    }
}
