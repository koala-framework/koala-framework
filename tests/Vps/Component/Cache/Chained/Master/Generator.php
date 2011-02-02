<?php
class Vps_Component_Cache_Chained_Master_Generator extends Vps_Component_Generator_Page_Table
{
    protected function _getParentDataByRow($row, $select)
    {
        return Vps_Component_Data_Root::getInstance()->getComponentById('root-master');
    }
}
