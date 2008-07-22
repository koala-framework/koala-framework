<?php
class Vps_Assets_GoogleMapsApiKey
{
    public static function getKey()
    {
        $hostParts = explode('.', $_SERVER['HTTP_HOST']);
        $configDomain = $hostParts[count($hostParts)-2]  // zB 'vivid-planet'
                        .$hostParts[count($hostParts)-1]; // zB 'com'
        if (isset(Zend_Registry::get('config')->googleMapsApiKeys->$configDomain)) {
            return Zend_Registry::get('config')->googleMapsApiKeys->$configDomain;
        }
        return '';
    }
}
