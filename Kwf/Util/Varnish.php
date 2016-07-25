<?php
class Kwf_Util_Varnish
{
    public function purge($url)
    {
        foreach (self::getVarnishDomains() as $domain) {
            $url = 'http://'.$domain.'/purge-url'.$url;
            $c = new Zend_Http_Client($url);
            $response = $c->request();
            if ($response->isError()) {
                throw new Kwf_Exception('purge failed: '.$response->getBody());
            }
        }
    }

    public function getVarnishDomains()
    {
        $domains = array();
        if (Kwf_Config::getValue('server.varnishDomain')) {
            $domains[] = Kwf_Config::getValue('server.varnishDomain');
        }
        foreach (Kwf_Config::getValueArray('kwc.domains') as $i) {
            if (isset($i['varnishDomain']) && $i['varnishDomain'] && !in_array($i['varnishDomain'], $domains)) {
                $domains[] = $i['varnishDomain'];
            }
        }
        return $domains;
    }
}
