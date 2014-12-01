<?php
class Kwf_Util_ClearCache_Types_Users extends Kwf_Util_ClearCache_Types_Table
{
    public function __construct()
    {
        parent::__construct('cache_users');
    }

    protected function _refreshCache($options)
    {
        Kwf_Model_Abstract::getInstance(Kwf_Config::getValue('user.kwfUserController.model'))
            ->synchronize(Kwf_Model_MirrorCache::SYNC_ALWAYS);
    }

    public function getTypeName()
    {
        return 'cache_users';
    }
    public function doesRefresh() { return true; }
    public function doesClear() { return true; }
}
