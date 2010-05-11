<?php
class Vps_Http_Pecl_Requestor extends Vps_Http_Requestor
{
    private $_cache = array();

    public function request($url)
    {
        if (array_key_exists($url, $this->_cache)) {
            return $this->_cache[$url];
        }
        $request = new HttpRequest($url, HTTP_METH_GET, $this->getRequestOptions());
        $response = $request->send();
        $this->_cache[$url] = new Vps_Http_Pecl_Requestor_Response($response);
        return $this->_cache[$url];
    }

    public function getRequestOptions()
    {
        $options = array();
        $options['redirect'] = 5;
        $options['timeout'] = 50;
        $options['connecttimeout'] = 30;
        $options['useragent'] = Vps_Registry::get('config')->httpUserAgent;
        return $options;
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
