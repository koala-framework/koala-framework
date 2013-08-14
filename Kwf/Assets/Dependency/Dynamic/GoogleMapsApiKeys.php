<?php
class Kwf_Assets_Dependency_Dynamic_GoogleMapsApiKeys extends Kwf_Assets_Dependency_File
{
    public function __construct()
    {
        parent::__construct(null);
    }

    public function getMimeType()
    {
        return 'text/javascript; charset=utf-8';
    }

    public function getContents($language)
    {
        $json = json_encode(Kwf_Config::getValueArray('googleMapsApiKeys'));
        return "Ext.namespace('Kwf.GoogleMap');\nKwf.GoogleMap.apiKeys = $json;\n";
    }

    public function getFileName()
    {
        return null;
    }

}
