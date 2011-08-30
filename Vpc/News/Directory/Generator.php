<?php
class Vpc_News_Directory_Generator extends Vps_Component_Generator_Page_Table
{
    protected function _formatSelect($parentData, $select)
    {
        $ret = parent::_formatSelect($parentData, $select);
        if (!$ret) return $ret;

        if (is_array($select)) $select = $select['select'];
        if (!$select->getPart(Vps_Component_Select::IGNORE_VISIBLE)) {
            $ret->where('publish_date <= NOW()');
            if (Vpc_Abstract::getSetting($this->_class, 'enableExpireDate')) {
                $ret->where('expiry_date >= NOW() OR ISNULL(expiry_date)');
            }
        }

        $ret->order('publish_date', 'DESC');
        return $ret;
    }
}
