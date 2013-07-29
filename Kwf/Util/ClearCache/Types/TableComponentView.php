<?php
class Kwf_Util_ClearCache_Types_TableComponentView extends Kwf_Util_ClearCache_Types_Table
{
    public function __construct()
    {
        parent::__construct('cache_component');
    }

    protected function _clearCache($options)
    {
        if (!Kwf_Config::getValue('debug.componentCache.clearOnClearCache')) {
            $this->_output("skipped: (won't delete, use clear-view-cache to clear)\n");
            return;
        }
        Kwf_Component_Cache::getInstance()->deleteViewCache(new Kwf_Model_Select());
    }

    public function getTypeName()
    {
        return 'cache_component';
    }
    public function doesRefresh() { return false; }
    public function doesClear() { return true; }
}
