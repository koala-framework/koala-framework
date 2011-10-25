<?php
class Kwf_Component_Generator_DbId_StaticGenerator extends Kwf_Component_Generator_Page_Table
{
    protected function _getParentDataByRow($row)
    {
        return Kwf_Component_Data_Root::getInstance()
            ->getChildComponent('_static');
    }
}
