<?php
class Vpc_Events_Directory_Generator extends Vps_Component_Generator_Page_Table
{
    protected function _formatSelect($parentData, $select)
    {
        $ret = parent::_formatSelect($parentData, $select);
        if (!$ret) return $ret;
        $ret->where('IF(ISNULL(end_date), start_date, end_date) >= NOW()');
        return $ret;
    }
}
