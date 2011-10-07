<?php
class Kwf_Component_Cache_Chained_Master_Generator extends Kwf_Component_Generator_Page_Table
{
    protected function _getParentDataByRow($row, $select)
    {
        return Kwf_Component_Data_Root::getInstance()->getComponentById('root-master');
    }
}
