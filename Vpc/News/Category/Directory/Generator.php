<?php
class Vpc_News_Category_Directory_Generator extends Vps_Component_Generator_Page_Table
{
    protected $_nameColumn = 'value';

    protected function _getSelect($parentData, $constraints)
    {
        $select = parent::_getSelect($parentData, $constraints);
        if (!$select) return null;
        $select->where('pool = ?', Vpc_Abstract::getSetting($this->_class, 'pool'));
        return $select;
    }
}
