<?php
class Kwf_Assets_Dependency_Dynamic_GoogleMapsApiKeys extends Kwf_Assets_Dependency_Abstract
{
    public function getMimeType()
    {
        return 'text/javascript';
    }

    public function getContentsPacked($language)
    {
        $json = json_encode(Kwf_Config::getValueArray('googleMapsApiKeys'));
        $ret = "module.exports = $json;";
        return Kwf_SourceMaps_SourceMap::createEmptyMap($ret);
    }

    public function usesLanguage()
    {
        return false;
    }

    public function getIdentifier()
    {
        return 'GoogleMapsApiKey';
    }
}
