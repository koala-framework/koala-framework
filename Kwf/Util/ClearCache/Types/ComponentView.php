<?php
class Kwf_Util_ClearCache_Types_ComponentView extends Kwf_Util_ClearCache_Types_Abstract
{
    protected function _clearCache($options)
    {
        if (!Kwf_Config::getValue('debug.componentCache.clearOnClearCache')) {
            $this->_output("skipped: (won't delete, use clear-view-cache to clear)\n");
            return;
        }
        if (!Kwf_Setup::hasDb()) {
            $this->_output("skipped: (no db configured)\n");
            return;
        }
        Kwf_Component_Cache::getInstance()->deleteViewCache(array());
    }

    public function getTypeName()
    {
        return 'componentView';
    }
    public function doesRefresh() { return false; }
    public function doesClear() { return true; }
}
