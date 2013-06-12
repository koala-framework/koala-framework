<?php
class Kwf_Util_ClearCache_Types_Trl extends Kwf_Util_ClearCache_Types_Dir
{
    public function __construct()
    {
        parent::__construct('trl');
        //TODO trl cached also in apc
    }

    protected function _refreshCache($options)
    {
        $webCodeLanguage = Kwf_Registry::get('config')->webCodeLanguage;
        if ($webCodeLanguage != 'en') {
            Kwf_Trl::getInstance()->trl('Login', array(), 'en', $webCodeLanguage);
        }
    }

    public function getTypeName()
    {
        return 'trl';
    }
    public function doesClear() { return true; }
    public function doesRefresh() { return true; }
}
