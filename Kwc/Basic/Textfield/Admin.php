<?php
class Kwc_Basic_Textfield_Admin extends Kwc_Abstract_Composite_Admin
{
    public function gridColumns()
    {
        $ret = array();
        $c = new Kwf_Grid_Column('string', trlKwf('Content'));
        $c->setData(new Kwf_Component_Abstract_ToStringData($this->_class));
        $ret['string'] = $c;
        return $ret;
    }
}
