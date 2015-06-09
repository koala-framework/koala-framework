<?php
class Kwf_Util_ClearCache_Types_Assets extends Kwf_Util_ClearCache_Types_Abstract
{
    protected function _clearCache($options)
    {
        Kwf_Assets_Cache::getInstance()->clean();
    }

    public function getTypeName()
    {
        return 'assets';
    }
    public function doesRefresh() { return false; }
    public function doesClear() { return true; }
}
