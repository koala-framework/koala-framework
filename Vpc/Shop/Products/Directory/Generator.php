<?php
class Vpc_Shop_Products_Directory_Generator extends Vps_Component_Generator_Page_Table
{
    protected function _getParentDataByRow($row)
    {
        return Vps_Component_Data_Root::getInstance()->getComponentsByClass(
            'Vpc_Shop_Products_Directory_Component'
        );
    }
}
