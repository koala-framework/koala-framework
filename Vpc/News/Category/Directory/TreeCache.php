<?php
class Vpc_News_Category_Directory_TreeCache extends Vpc_TreeCache_TablePage
{
    protected $_tableName = 'Vps_Dao_Pool';
    protected $_childClassKey = 'detail';
    protected $_nameColumn = 'value';
    protected $_filenameColumn = 'id';

    protected function _getSelect($constraints)
    {
        $select = parent::_getSelect($constraints);
        $select->where('pool = ?', $this->_getSetting('pool'));
        return $select;
    }
}
