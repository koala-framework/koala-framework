<?php
class Kwf_Assets_Provider_Dynamic extends Kwf_Assets_Provider_Abstract
{
    public function getDependency($dependencyName)
    {
        if ($dependencyName == 'DynamicGoogleMapsApiKeys') {
            return new Kwf_Assets_Dependency_Dynamic_GoogleMapsApiKeys();
        } else if ($dependencyName == 'Kwf.CountriesData') {
            return new Kwf_Assets_Dependency_Dynamic_CountriesData();
        } else if ($dependencyName == 'DynamicFacebookAppId') {
            return new Kwf_Assets_Dependency_Dynamic_FacebookAppId();
        } else if ($dependencyName == 'KwfDynamicAssetsVersion') {
            return new Kwf_Assets_Dependency_Dynamic_AssetsVersion();
        }
    }
}
