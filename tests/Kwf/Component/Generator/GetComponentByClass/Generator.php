<?php
class Kwf_Component_Generator_GetComponentByClass_Generator extends Kwf_Component_Generator_Table
{
    protected function _getParentDataByRow($row)
    {
        return Kwf_Component_Data_Root::getInstance()->getComponentById('1');
    }
}
