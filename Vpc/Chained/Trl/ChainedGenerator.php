<?php
class Vpc_Chained_Trl_ChainedGenerator extends Vpc_Chained_Abstract_ChainedGenerator
{
    public function getPagesControllerConfig($component, $generatorClass = null)
    {
        $ret = parent::getPagesControllerConfig($component, $generatorClass);
        $ret['icon'] = 'plugin';
        return $ret;
    }

    protected function _formatSelect($parentData, $select)
    {
        $ret = parent::_formatSelect($parentData, $select);
        $ret->whereEquals('master', false);
        return $ret;
    }
}