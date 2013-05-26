<?php
class Kwf_Util_ClearCache_Types_Setup extends Kwf_Util_ClearCache_Types_Abstract
{
    protected function _clearCache($options)
    {
        if (file_exists('cache/setup.php')) {
            unlink('cache/setup.php');
        }
    }

    protected function _refreshCache($options)
    {
        file_put_contents('cache/setup.php', Kwf_Util_Setup::generateCode(Kwf_Setup::$configClass));
        Kwf_Util_Apc::callClearCacheByCli(array('files' => getcwd().'/cache/setup.php'), Kwf_Util_Apc::SILENT);
    }

    public function getTypeName()
    {
        return 'setup';
    }
    public function doesRefresh() { return true; }
    public function doesClear() { return true; }
}
