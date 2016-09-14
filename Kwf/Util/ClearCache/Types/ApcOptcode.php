<?php
class Kwf_Util_ClearCache_Types_ApcOptcode extends Kwf_Util_ClearCache_Types_Abstract
{
    public function outputFn($msg)
    {
        $this->_output($msg);
    }

    protected function _clearCache($options)
    {
        Kwf_Util_ClearCache::clearOptcode();
    }

    public function getTypeName()
    {
        return 'optcode';
    }
    public function doesRefresh() { return false; }
    public function doesClear() { return true; }
}
