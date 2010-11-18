<?php
class Vpc_Basic_Link_Admin extends Vpc_Abstract_Composite_Admin
{
    public function gridColumns()
    {
        $ret = array();
        $c = new Vps_Grid_Column('string', trlVps('Linktext'));
        $c->setData(new Vps_Component_Abstract_ToStringData($this->_class));
        $ret['string'] = $c;
        $ret = array_merge($ret, parent::gridColumns());
        return $ret;
    }
}
