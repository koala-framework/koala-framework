<?php
class Kwc_Events_Directory_Generator extends Kwf_Component_Generator_Page_Table
{
    protected function _formatSelect($parentData, $select)
    {
        $ret = parent::_formatSelect($parentData, $select);
        if (!$ret) return $ret;
        $ret->order('start_date', 'ASC');
        return $ret;
    }
}
