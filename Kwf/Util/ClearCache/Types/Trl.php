<?php
class Kwf_Util_ClearCache_Types_Trl extends Kwf_Util_ClearCache_Types_Dir
{
    public function __construct()
    {
        parent::__construct('trl');
    }

    protected function _clearCache($options)
    {
        parent::_clearCache($options);
        Kwf_Cache_SimpleStatic::clear('trl-');
    }

    protected function _refreshCache($options)
    {
        $webCodeLanguage = Kwf_Registry::get('config')->webCodeLanguage;
        if ($webCodeLanguage != 'en') {
            Kwf_Trl::getInstance()->trlKwf('Login', array(), $webCodeLanguage);
        }
    }

    public function getTypeName()
    {
        return 'trl';
    }
    public function doesClear() { return true; }
    public function doesRefresh() { return true; }
}
