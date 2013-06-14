<?php
class Kwf_Util_ClearCache_Types_Config extends Kwf_Util_ClearCache_Types_Dir
{
    public function __construct()
    {
        parent::__construct('config');
        //TODO what about other caches (apc)?
    }

    protected function _refreshCache($options)
    {
        Kwf_Config_Web::reload();
    }

    public function getTypeName()
    {
        return 'config';
    }
    public function doesRefresh() { return true; }
    public function doesClear() { return true; }
}
