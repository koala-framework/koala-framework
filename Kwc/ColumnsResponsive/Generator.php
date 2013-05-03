<?php
class Kwc_ColumnsResponsive_Generator extends Kwf_Component_Generator_Table
{
    protected function _fetchRows($parentData, $select)
    {
        $this->_getModel()->setData($parentData->componentClass, $parentData->componentId);
        return $this->_getModel()->getRows($select);
    }
}
