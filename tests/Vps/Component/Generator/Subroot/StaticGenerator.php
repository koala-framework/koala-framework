<?php
class Vps_Component_Generator_Subroot_StaticGenerator extends Vps_Component_Generator_Page_Table
{
    protected function _getParentDataByRow($row, $select)
    {
        if ($row->id == 3) {
            return array(Vps_Component_Data_Root::getInstance()->getComponentById(
                'root-ch_static'
            ));
        } else {
            return Vps_Component_Data_Root::getInstance()->getComponentsByClass(
                'Vps_Component_Generator_Subroot_Static'
            );
        }
    }
}
