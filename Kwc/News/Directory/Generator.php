<?php
class Kwc_News_Directory_Generator extends Kwf_Component_Generator_Page_Table
{
    protected $_eventsClass = 'Kwc_News_Directory_GeneratorEvents';

    protected function _formatSelect($parentData, $select)
    {
        $ret = parent::_formatSelect($parentData, $select);
        if (!$ret) return $ret;

        if (!$select || !$select->getPart(Kwf_Component_Select::IGNORE_VISIBLE)) {
            $ret->where('publish_date <= CURDATE()');
            if (Kwc_Abstract::getSetting($this->_class, 'enableExpireDate')) {
                $ret->where('expiry_date >= CURDATE() OR ISNULL(expiry_date)');
            }
        }

        $ret->order('publish_date', 'DESC');
        return $ret;
    }
}
