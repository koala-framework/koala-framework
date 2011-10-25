<?php
class Kwf_Component_Generator_Subroot_StaticGenerator extends Kwf_Component_Generator_Page_Table
{
    protected function _getParentDataByRow($row, $select)
    {
        if ($row->id == 3) {
            return array(Kwf_Component_Data_Root::getInstance()->getComponentById(
                'root-ch_static'
            ));
        } else {
            return Kwf_Component_Data_Root::getInstance()->getComponentsByClass(
                'Kwf_Component_Generator_Subroot_Static'
            );
        }
    }
}
