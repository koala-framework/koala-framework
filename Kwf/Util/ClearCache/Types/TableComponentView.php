<?php
class Kwf_Util_ClearCache_Types_TableComponentView extends Kwf_Util_ClearCache_Types_Table
{
    public function __construct()
    {
        parent::__construct('cache_component');
    }

    protected function _clearCache($options)
    {
        try {
            $cnt = Zend_Registry::get('db')->query("SELECT COUNT(*) FROM cache_component WHERE deleted=0")->fetchColumn();
            if ($cnt > 5000) {
                $this->_output("skipped: (won't delete $cnt entries, use clear-view-cache to clear)\n");
                return;
            }
        } catch (Exception $e) {}
        parent::_clearCache($options);
    }

    public function getTypeName()
    {
        return 'cache_component';
    }
    public function doesRefresh() { return false; }
    public function doesClear() { return true; }
}
