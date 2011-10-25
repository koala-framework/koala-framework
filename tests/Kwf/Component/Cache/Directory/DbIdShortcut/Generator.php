<?php
class Kwf_Component_Cache_Directory_DbIdShortcut_Generator extends Kwf_Component_Generator_Table
{
    protected function _getParentDataByRow($row, $select)
    {
        return Kwf_Component_Data_Root::getInstance();
    }
}
