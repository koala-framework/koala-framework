<?php
class Vps_Component_Generator_DbId_StaticGenerator extends Vps_Component_Generator_Page_Table
{
    protected function _getParentDataByRow($row)
    {
        return Vps_Component_Data_Root::getInstance()
            ->getChildComponent('_static');
    }
}
