<?php
class Vpc_Events_Directory_Generator extends Vps_Component_Generator_Page_Table
{
    protected function _formatSelect($parentData, $select)
    {
        $ret = parent::_formatSelect($parentData, $select);
        if (!$ret) return $ret;
        $ret->where('start_date <= NOW()');
        $ret->where('end_date >= NOW() OR ISNULL(end_date)');
        $ret->order('start_date', 'ASC');
        return $ret;
    }
}
