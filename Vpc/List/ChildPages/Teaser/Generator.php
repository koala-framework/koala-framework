<?php
class Vpc_List_ChildPages_Teaser_Generator extends Vps_Component_Generator_Table
{
    protected $_hasNumericIds = false;

    protected function _formatSelect($parentData, $select)
    {
        $select = parent::_formatSelect($parentData, $select);
        if (!$select) return $select;

        if (!$parentData) {
            throw new Vps_Exception_NotYetImplemented();
        }
        $select->whereEquals('parent_component_id', $parentData->componentId);
        return $select;
    }

    protected function _formatConfig($parentData, $row)
    {
        $ret = parent::_formatConfig($parentData, $row);
        $ret['targetPage'] = Vps_Component_Data_Root::getInstance()
            ->getComponentById($row->target_page_id);
        return $ret;
    }
}
