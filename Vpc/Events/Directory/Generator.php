<?php
class Vpc_Events_Directory_Generator extends Vpc_News_Directory_Generator
{
    protected function _formatSelect($parentData, $select)
    {
        $ret = Vpc_Directories_ItemPage_Directory_Component::_formatSelect($parentData, $select);
        if (!$ret) return $ret;
        $ret->order('start_date', 'ASC');
        return $ret;
    }
}
