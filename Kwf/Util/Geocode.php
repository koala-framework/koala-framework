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
        $q = $address;
        $q = str_replace(array('ä','ö','ü','Ä','Ö','Ü','ß'), array('ae','oe','ue','Ae','Oe','Ue','ss'), $q);
        $getParams = array(
            'address' => $q,
            'sensor' => 'false'
        );

        $client = new Zend_Http_Client("http://maps.googleapis.com/maps/api/geocode/json");
        $client->setMethod(Zend_Http_Client::GET);
        $client->setParameterGet($getParams);
        $body = utf8_encode($client->request()->getBody());

        // da gibts einen php bug in älteren versionen, der verursacht,
        // dass json_decode() bei gesetztem "setlocale(LC_NUMERIC, 'de_DE');"
        // float zahlen mit punkt als integer interpretiert.
        // php bugtracker: http://bugs.php.net/bug.php?id=41403
        if (version_compare(PHP_VERSION, '5.2.3') == -1) {
            setlocale(LC_NUMERIC, 'C');
            $result = Zend_Json::decode($body);
            setlocale(LC_NUMERIC, explode(', ', trlcKwf('locale', 'C')));
        } else {
            try {
                $result = Zend_Json::decode($body);
            } catch (Zend_Json_Exception $e) {
                $e = new Kwf_Exception_Other($e);
                $e->logOrThrow();
            }
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
