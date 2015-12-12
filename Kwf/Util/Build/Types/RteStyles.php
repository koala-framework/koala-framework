<?php
class Kwf_Util_Build_Types_RteStyles extends Kwf_Util_Build_Types_Abstract
{
    protected function _build($options)
    {
        $package = Kwf_Assets_Package_Default::getInstance('Frontend');
        $ret = array();
        foreach ($package->getDependency()->getFilteredUniqueDependencies('text/css') as $dep) {
            $ret = array_merge($ret, Kwc_Basic_Text_StylesModel::parseMasterStyles($dep->getContentsSourceString()));
        }
        $fileName = 'build/assets/rte-styles';
        file_put_contents($fileName, json_encode($ret));
    }

    public function getTypeName()
    {
        return 'rte-styles';
    }
}
