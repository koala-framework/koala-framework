<?php
class Kwc_Columns_Generator extends Kwf_Component_Generator_Table
{
    protected function _fetchRows($parentData, $select)
    {
        if ($select->hasPart(Kwf_Component_Select::WHERE_ID)) {
            return array();
        }
        if (!$parentData) return array(); //TODO this is wrong, solve correctly

        $this->_getModel()->setData($parentData->componentClass, $parentData->dbId);
        return $this->_getModel()->getRows($select);
    }
}
