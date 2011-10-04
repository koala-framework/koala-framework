<?php
class Vpc_Root_DomainRoot_Generator extends Vps_Component_Generator_PseudoPage_Table
{
    protected $_hasNumericIds = false;
    protected $_nameColumn = 'name';
    protected $_filenameColumn = 'id';
    protected $_uniqueFilename = true;
    protected $_inherits = true;

    public function getPagesControllerConfig($component, $generatorClass = null)
    {
        $ret = parent::getPagesControllerConfig($component, $generatorClass);
        $ret['icon'] = 'world';
        $ret['expanded'] = true;
        return $ret;
    }

    protected function _formatConfig($parentData, $id)
    {
        $ret = parent::_formatConfig($parentData, $id);
        $ret['name'] = $ret['row']->name;
        return $ret;
    }

    protected function _formatSelect($parentData, $select)
    {
        $select = parent::_formatSelect($parentData, $select);
        return $select;
    }

    protected function _getParentDataByRow($row)
    {
        return Vps_Component_Data_Root::getInstance();
    }

    public function getGeneratorFlags()
    {
        $ret = parent::getGeneratorFlags();
        $ret['showInPageTreeAdmin'] = true;
        return $ret;
    }
}
