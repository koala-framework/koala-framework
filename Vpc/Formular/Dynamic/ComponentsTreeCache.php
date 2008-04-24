<?php
class Vpc_Formular_Dynamic_ComponentsTreeCache extends Vpc_TreeCache_Table
{
    protected $_componentClass = 'component_class';

    protected function _getWhere()
    {
        $ret = parent::_getWhere();
        $ret[] = "LEFT(t.component_class, 4)='Vpc_'";
        return $ret;
    }
}
