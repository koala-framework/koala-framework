<?php
class Vps_Component_Cache_Directory_DbIdShortcut_Generator extends Vps_Component_Generator_Table
{
    protected function _getParentDataByRow($row, $select)
    {
        return Vps_Component_Data_Root::getInstance();
    }
}
