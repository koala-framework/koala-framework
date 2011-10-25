<?php
class Kwc_Composite_TextImageLink_Admin extends Kwc_Abstract_Composite_Admin
{
    public function gridColumns()
    {
        $ret = array();
        $c = new Kwf_Grid_Column('string', trlKwf('Linktext'));
        $c->setData(new Kwf_Component_Abstract_ToStringData($this->_class));
        $ret['string'] = $c;
        $ret = array_merge($ret, parent::gridColumns());
        return $ret;
    }
}
