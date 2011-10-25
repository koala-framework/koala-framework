<?php
class Kwf_Component_Cache_TestCallbackRoot extends Kwf_Component_NoCategoriesRoot
{
    private $_callbacks = array();

    public static function getSettings()
    {
        $ret = parent::getSettings();
        unset($ret['generators']['page']);
        return $ret;
    }

    public function getCallbacks()
    {
        return $this->_callbacks;
    }

    public function onCacheCallback($row)
    {
        $this->_callbacks[] = $row;
    }
}
