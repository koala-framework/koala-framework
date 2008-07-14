<?php
class Vpc_Formular_Dynamic_Generator extends Vps_Component_Generator_Table
{
    protected $_tableName = 'Vpc_Formular_Dynamic_Model';

    protected function _getWhere()
    {
        $ret = parent::_getWhere();
        $ret[] = "LEFT(t.component_class, 4)='Vpc_'";
        return $ret;
    }
}
