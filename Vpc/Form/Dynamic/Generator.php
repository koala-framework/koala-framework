<?php
class Vpc_Form_Dynamic_Generator extends Vps_Component_Generator_Table
{
    protected function _getWhere()
    {
        $ret = parent::_getWhere();
        $ret[] = "LEFT(t.component_class, 4)='Vpc_'";
        return $ret;
    }
}
