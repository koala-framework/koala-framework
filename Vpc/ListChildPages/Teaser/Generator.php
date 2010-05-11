<?php
class Vpc_ListChildPages_Teaser_Generator extends Vps_Component_Generator_Table
{
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
}
