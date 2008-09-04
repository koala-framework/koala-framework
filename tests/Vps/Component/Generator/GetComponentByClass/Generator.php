<?php
class Vps_Component_Generator_GetComponentByClass_Generator extends Vps_Component_Generator_Table
{
    protected function _getParentDataByRow($row)
    {
        return Vps_Component_Data_Root::getInstance()->getComponentById('1');
    }
}
