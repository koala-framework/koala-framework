<?php
class Kwf_Util_ClearCache_Types_Table extends Kwf_Util_ClearCache_Types_Abstract
{
    private $_table;
    public function __construct($table)
    {
        $this->_table = $table;
    }

    protected function _clearCache($options)
    {
        Zend_Registry::get('db')->query("TRUNCATE TABLE $this->_table");
    }

    public function getTypeName()
    {
        return $this->_table;
    }
    public function doesRefresh() { return false; }
    public function doesClear() { return true; }
}
