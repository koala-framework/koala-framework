<?php
abstract class Vps_Db_Table_Abstract extends Zend_Db_Table_Abstract
{
    private $_dao;
    protected $_rowClass = 'Vps_Db_Table_Row';
    protected $_rowsetClass = 'Vps_Db_Table_Rowset';
    protected $_filters = array();

    protected function _setup()
    {
        parent::_setup();
        $this->_setupFilters();
    }

    protected function _setupDatabaseAdapter()
    {
        //instead of setDefaultAdapter - this one layz loads
        if (! $this->_db) {
            $this->_db = Zend_Registry::get('db');
        }
    }

    protected function _setupFilters()
    {
    }

    public function getFilters()
    {
        return $this->_filters;
    }

    public function setDao($dao)
    {
        $this->_dao = $dao;
    }

    public function getDao()
    {
        return $this->_dao;
    }

    public function numberize($id, $fieldname, $value, array $where = array())
    {
        $row = $this->find($id)->current();
        if ($row) {
            return $row->numberize($fieldname, $value, $where);
        } else {
            return false;
        }
    }
    
    public function numberizeAll($fieldname, $where = array())
    {
        $rows = $this->fetchAll($where, $fieldname);
        $i = 1;
        foreach ($rows as $row) {
            if ($row->$fieldname != $i) {
                $row->$fieldname = $i;
                $row->save();
            }
            $i++;
        }
    }
}
