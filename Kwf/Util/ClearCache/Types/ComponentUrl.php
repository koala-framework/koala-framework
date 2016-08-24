<?php
class Kwf_Util_ClearCache_Types_ComponentUrl extends Kwf_Util_ClearCache_Types_Abstract
{
    protected function _clearCache($options)
    {
        Kwf_Component_Cache_Url_Abstract::getInstance()->clear();
    }

    public function getTypeName()
    {
        return 'componentUrl';
    }
    public function doesRefresh() { return false; }
    public function doesClear() { return true; }
}
