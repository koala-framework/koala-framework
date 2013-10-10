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
        foreach (glob('cache/model/zend_cache---trl_*') as $f) {
            unlink($f);
        }
        foreach (glob('cache/model/zend_cache---internal-metadatas---trl_*') as $f) {
            unlink($f);
        }
        Kwf_Cache_SimpleStatic::clear('trl-');
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
