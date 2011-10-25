<?php
class Kwc_User_Directory_Generator extends Kwf_Component_Generator_Page_Table
{
    protected function _getParentDataByRow($row)
    {
        return Kwf_Component_Data_Root::getInstance()->getComponentsByClass(
            'Kwc_User_Directory_Component'
        );
    }
}
