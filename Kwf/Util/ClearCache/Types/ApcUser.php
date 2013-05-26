<?php
class Kwf_Util_ClearCache_Types_ApcUser extends Kwf_Util_ClearCache_Types_Abstract
{
    protected function _clearCache($options)
    {
        Kwf_Util_Apc::callClearCacheByCli(array('type' => 'user'), $this->_verbosity == self::VERBOSE ? Kwf_Util_Apc::VERBOSE : Kwf_Util_Apc::SILENT, $options);
    }

    public function getTypeName()
    {
        return 'apc';
    }
    public function doesRefresh() { return false; }
    public function doesClear() { return true; }
}
