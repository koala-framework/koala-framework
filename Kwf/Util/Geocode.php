<?php
class Kwf_Util_Geocode
{
    /**
     * Gibt die Geokoordinaten anhand einer Adresse zurück
     *
     * @param string $address Die Adresse die geocodet werden woll
     * @return array|null $geocode Ein Array mit key 'lat' und 'lng'
     */
    public static function getCoordinates($address)
    {
        $config = Kwf_Registry::get('config');
        $q = $address;
        $q = str_replace(array('ä','ö','ü','Ä','Ö','Ü','ß'), array('ae','oe','ue','Ae','Oe','Ue','ss'), $q);
        $getParams = array(
            'address' => $q,
            'sensor' => 'false',
            'key' => $config->googleServerToServerApiKey
        );

        $httpClientConfig = array(
            'timeout' => 20,
            'persistent' => false
        );

        if ($config->http && $config->http->proxy && $config->http->proxy->host && $config->http->proxy->port) {
            $httpClientConfig['adapter'] = 'Zend_Http_Client_Adapter_Proxy';
            $httpClientConfig['proxy_host'] = $config->http->proxy->host;
            $httpClientConfig['proxy_port'] = $config->http->proxy->port;
        }

        $client = new Zend_Http_Client("https://maps.googleapis.com/maps/api/geocode/json", $httpClientConfig);
        $client->setMethod(Zend_Http_Client::GET);
        $client->setParameterGet($getParams);
        $body = utf8_encode($client->request()->getBody());

        try {
            $result = Zend_Json::decode($body);
        } catch (Zend_Json_Exception $e) {
            $e = new Kwf_Exception_Other($e);
            $e->logOrThrow();
        }

        if (isset($result['results'][0]['geometry']['location']['lat'])
            && isset($result['results'][0]['geometry']['location']['lng'])
        ) {
            return array(
                'lat' => $result['results'][0]['geometry']['location']['lat'],
                'lng' => $result['results'][0]['geometry']['location']['lng']
            );
        }
        return null;
    }

}
