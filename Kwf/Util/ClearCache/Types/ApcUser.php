<?php
class Kwf_Util_ClearCache_Types_ApcUser extends Kwf_Util_ClearCache_Types_Abstract
{
    public function outputFn($msg)
    {
        $this->_output($msg);
    }

    protected function _clearCache($options)
    {
        Kwf_Util_ClearCache::clearApcUser();
    }

    public function getTypeName()
    {
        return 'apc';
    }
    public function doesRefresh() { return false; }
    public function doesClear() { return true; }
}
