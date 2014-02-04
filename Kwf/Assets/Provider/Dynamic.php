<?php
class Kwf_Assets_Provider_Dynamic extends Kwf_Assets_Provider_Abstract
{
    public function getDependency($dependencyName)
    {
        if ($dependencyName == 'DynamicGoogleMapsApiKeys') {
            return new Kwf_Assets_Dependency_Dynamic_GoogleMapsApiKeys();
        } else if ($dependencyName == 'Kwf.CountriesData') {
            return new Kwf_Assets_Dependency_Dynamic_CountriesData();
        }
    }
}
