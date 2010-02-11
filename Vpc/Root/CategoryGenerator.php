<?php
class Vpc_Root_CategoryGenerator extends Vps_Component_Generator_Table
{
    protected $_hasNumericIds = false;
    protected $_inherits = true;

    public function getPagesControllerConfig()
    {
        $ret = parent::getPagesControllerConfig();
        $ret['icon'] = 'folder';
        $ret['disabledIcon'] = 'folder';
        $ret['expanded'] = true;
        return $ret;
    }

    protected function _formatConfig($parentData, $id)
    {
        $ret = parent::_formatConfig($parentData, $id);
        $ret['name'] = $ret['row']->name;
        return $ret;
    }

    protected function _getParentDataByRow($row)
    {
        return Vps_Component_Data_Root::getInstance();
    }
}
