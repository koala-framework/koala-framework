<?php
class Kwf_Util_ClearCache_Types_Yac extends Kwf_Util_ClearCache_Types_Abstract
{
    public function outputFn($msg)
    {
        $this->_output($msg);
    }

    protected function _clearCache($options)
    {
        $options['outputFn'] = array($this, 'outputFn');
        Kwf_Util_Apc::callClearCacheByCli(array('type' => 'yac'), $options);
    }

    public function getTypeName()
    {
        return 'yac';
    }
    public function doesRefresh() { return false; }
    public function doesClear() { return true; }
}
