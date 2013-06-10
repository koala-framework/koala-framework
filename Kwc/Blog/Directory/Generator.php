<?php
class Kwc_Blog_Directory_Generator extends Kwf_Component_Generator_Page_Table
{
    protected function _formatSelect($parentData, $select)
    {
        $ret = parent::_formatSelect($parentData, $select);
        if (!$ret) return $ret;

        if (!$select || !$select->getPart(Kwf_Component_Select::IGNORE_VISIBLE)) {
            $ret->where('publish_date <= NOW()');
        }

        $ret->order('publish_date', 'DESC');
        return $ret;
    }
}
