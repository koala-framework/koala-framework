<?php
class Kwf_Assets_GoogleMapsApiKey
{
    public static function getKey()
    {
        if (isset($_SERVER['HTTP_HOST'])) {
            $host = $_SERVER['HTTP_HOST'];
        } else {
            $host = Kwf_Registry::get('config')->server->domain;
        }

        $configDomain = self::getConfigDomain($host);
        if (isset(Kwf_Registry::get('config')->googleMapsApiKeys->$configDomain)) {
            return Kwf_Registry::get('config')->googleMapsApiKeys->$configDomain;
        }
        return '';
    }

    // for unit testing only
    public static function getConfigDomain($host)
    {
        if (!$host) return null;

        $longDomainEndings = array('or.at', 'co.at', 'gv.at', 'co.uk');

        // cleaning out the dots of long domain endings
        foreach ($longDomainEndings as $k => $v) {
            $longDomainEndings[$k] = str_replace('.', '', $v);
        }

        $hostParts = explode('.', $host);
        if (count($hostParts) <= 1) {
            $ret = $host;
        } else {
            $ret = $hostParts[count($hostParts)-2]  // zB 'vivid-planet'
                            .$hostParts[count($hostParts)-1]; // zB 'com'
        }
        if (in_array($ret, $longDomainEndings)) {
            $ret = $hostParts[count($hostParts)-3].$ret;
        }
        return $ret;
    }
}
