<?php
class Kwf_Util_ClearCache_Types_ApcOptcode extends Kwf_Util_ClearCache_Types_Abstract
{
    protected function _clearCache($options)
    {
        Kwf_Util_Apc::callClearCacheByCli(array('type' => 'file'), $this->_verbosity == self::VERBOSE ? Kwf_Util_Apc::VERBOSE : Kwf_Util_Apc::SILENT, $options);
    }

    public function getTypeName()
    {
        return 'optcode';
    }
    public function doesRefresh() { return false; }
    public function doesClear() { return true; }
}
