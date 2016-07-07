<?php
class Kwf_Assets_Dependency_Dynamic_GoogleMapsApiKeys extends Kwf_Assets_Dependency_Abstract
{
    public function getMimeType()
    {
        return 'text/javascript';
    }

    public function getContentsPacked()
    {
        if (!Kwf_Config::getValue('googleMapsApiKey') && !Kwf_Config::getValueArray('googleMapsApiKeys')) {
            throw new Kwf_Exception('googleMapsApiKey is required in config.ini');
        }
        if (Kwf_Config::getValue('googleMapsApiKey')) {
            $json = Kwf_Config::getValue('googleMapsApiKey');
            if (Kwf_Config::getValueArray('googleMapsApiKeys')) {
                throw new Kwf_Exception('Don\'t use googleMapsApiKeys and googleMapsApiKey together, remove googleMapsApiKeys');
            }
        } else {
             //legacy
            $json = Kwf_Config::getValueArray('googleMapsApiKeys');
        }
        $json = json_encode($json);
        $ret = "module.exports = $json;";
        $ret = Kwf_SourceMaps_SourceMap::createEmptyMap($ret);

        $data = $ret->getMapContentsData();
        $data->{'_x_org_koala-framework_masterFiles'} = array(
            'config.ini'
        );

        return $ret;
    }

    public function getIdentifier()
    {
        return 'GoogleMapsApiKeys';
    }
}
