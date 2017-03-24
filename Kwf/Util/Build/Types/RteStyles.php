<?php
class Kwf_Util_Build_Types_RteStyles extends Kwf_Util_Build_Types_Abstract
{
    protected function _build($options)
    {
        $ret = Kwc_Basic_Text_StylesModel::parseMasterStyles(file_get_contents('build/assets/Frontend.css'));
        $fileName = 'build/assets/rte-styles';
        file_put_contents($fileName, json_encode($ret));
    }

    public function getTypeName()
    {
        return 'rte-styles';
    }
}
