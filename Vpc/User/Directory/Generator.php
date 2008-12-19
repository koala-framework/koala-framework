<?php
class Vpc_User_Directory_Generator extends Vps_Component_Generator_Page_Table
{
    protected function _getParentDataByRow($row)
    {
        return Vps_Component_Data_Root::getInstance()->getComponentsByClass(
            'Vpc_User_Directory_Component'
        );
    }
}
