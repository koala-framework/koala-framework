<?php
class Kwc_Basic_LinkTag_ComponentClass_Data extends Kwc_Basic_LinkTag_Intern_Data
{
    protected function _getData()
    {
        if (($row = $this->_getRow()) && $row->target_component_id) {
            return Kwf_Component_Data_Root::getInstance()
                ->getComponentByDbId($row->target_component_id, array('subroot' => $this));
        }
        return false;
    }
}
