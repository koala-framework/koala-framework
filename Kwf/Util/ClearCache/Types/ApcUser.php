<?php
class Kwf_Util_ClearCache_Types_ApcUser extends Kwf_Util_ClearCache_Types_Abstract
{
    public function outputFn($msg)
    {
        $this->_output($msg);
    }

    protected function _clearCache($options)
    {
        $options['outputFn'] = array($this, 'outputFn');
        if (php_sapi_name() == 'cli') {
            Kwf_Util_Apc::callClearCacheByCli(array('type' => 'user'), $options);
        } else {
            if (extension_loaded('apcu')) {
                apc_clear_cache();
            } else {
                apc_clear_cache('user');
            }
        }
    }

    public function getTypeName()
    {
        return 'apc';
    }
    public function doesRefresh() { return false; }
    public function doesClear() { return true; }
}
