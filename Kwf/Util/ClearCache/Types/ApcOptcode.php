<?php
class Kwf_Util_ClearCache_Types_ApcOptcode extends Kwf_Util_ClearCache_Types_Abstract
{
    public function outputFn($msg)
    {
        $this->_output($msg);
    }

    protected function _clearCache($options)
    {
        $options['outputFn'] = array($this, 'outputFn');
        if (PHP_SAPI == 'cli') {
            Kwf_Util_Apc::callClearCacheByCli(array('type' => 'file'), $options);
        } else {
            if (!extension_loaded('apcu')) {
                apc_clear_cache('file');
            }
        }
    }

    public function getTypeName()
    {
        return 'optcode';
    }
    public function doesRefresh() { return false; }
    public function doesClear() { return true; }
}
