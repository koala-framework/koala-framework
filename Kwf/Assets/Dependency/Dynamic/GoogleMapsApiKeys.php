<?php
class Kwf_Assets_Dependency_Dynamic_GoogleMapsApiKeys extends Kwf_Assets_Dependency_Abstract
{
    public function getMimeType()
    {
        return 'text/javascript';
    }

    public function getContents($language)
    {
        $json = json_encode(Kwf_Config::getValueArray('googleMapsApiKeys'));
        return "Ext2.namespace('Kwf.GoogleMap');Kwf.GoogleMap.apiKeys = $json;";
    }
}
