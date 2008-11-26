<?php
class Vpc_Root_DomainRoot_Generator extends Vps_Component_Generator_PseudoPage_Table
{
    protected $_hasNumericIds = false;
    protected $_nameColumn = 'name';
    protected $_filenameColumn = 'id';
    protected $_uniqueFilename = true;

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
