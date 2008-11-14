<?php
class Vps_Assets_GoogleMapsApiKey
{
    public static function getKey()
    {
        if (isset($_SERVER['HTTP_HOST'])) {
            $host = $_SERVER['HTTP_HOST'];
        } else {
            $host = Vps_Registry::get('config')->server->domain;
        }

        $hostParts = explode('.', $host);
        $configDomain = $hostParts[count($hostParts)-2]  // zB 'vivid-planet'
                        .$hostParts[count($hostParts)-1]; // zB 'com'
        if (isset(Vps_Registry::get('config')->googleMapsApiKeys->$configDomain)) {
            return Vps_Registry::get('config')->googleMapsApiKeys->$configDomain;
        }
        return '';
    }
}
