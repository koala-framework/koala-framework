<?php
class Kwf_Assets_Provider_DefaultAssets extends Kwf_Assets_Provider_Abstract
{
    public function getDependency($dependencyName)
    {
        return null;
    }

    public function getDefaultDependencies()
    {
        $deps = array(
            'Kwf.AssetsVersion',
            'Kwf.Trl'
        );
        $ret = array();
        foreach ($deps as $i)  {
            $j = $this->_providerList->findDependency($i);
            if (!$j) {
                throw new Kwf_Exception("Didn't find dependency '$i'");
            }
            $ret[] = $j;
        }
        return $ret;
    }
}
