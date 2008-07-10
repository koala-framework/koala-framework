<?php
class Vpc_News_Category_Directory_TreeCache extends Vps_Component_Generator_TablePage
{
    protected $_childClassKey = 'detail';
    protected $_nameColumn = 'value';
    protected $_filenameColumn = 'id';

    protected function _getSelect($parentData, $constraints)
    {
        $select = parent::_getSelect($parentData, $constraints);
        if (!$select) return null;
        $select->where('pool = ?', $this->_getSetting('pool'));
        return $select;
    }
}
