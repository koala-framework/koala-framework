<?php
class Vpc_Chained_Trl_ChainedGenerator extends Vpc_Chained_Abstract_ChainedGenerator
{
    protected function _formatSelect($parentData, $select)
    {
        $ret = parent::_formatSelect($parentData, $select);
        $ret->whereEquals('master', false);
        return $ret;
    }
}