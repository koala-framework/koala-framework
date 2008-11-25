<?php
class Vpc_Root_CategoryGenerator extends Vps_Component_Generator_Table
{
    protected $_hasNumericIds = false;

    protected function _formatConfig($parentData, $id)
    {
        $ret = parent::_formatConfig($parentData, $id);
        $ret['inherits'] = true;
        $ret['name'] = $ret['row']->name;
        return $ret;
    }

    protected function _getParentDataByRow($row)
    {
        return Vps_Component_Data_Root::getInstance();
    }
}
