<?php
class Vps_Http_Pecl_Requestor extends Vps_Http_Requestor
{
    protected function _request($url)
    {
        $request = new HttpRequest($url, HTTP_METH_GET, $this->getRequestOptions());
        $response = $request->send();
        return new Vps_Http_Pecl_Requestor_Response($response);
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
}
