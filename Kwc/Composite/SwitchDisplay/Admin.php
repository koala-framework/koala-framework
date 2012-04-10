<?php
class Kwc_Composite_SwitchDisplay_Admin extends Kwc_Abstract_Composite_Admin
{
    public function gridColumns()
    {
        $ret = array();
        $c = new Kwf_Grid_Column('string', trlKwf('Link Text'));
        $c->setData(new Kwf_Component_Abstract_ToStringData($this->_class));
        $ret['string'] = $c;
        $ret = array_merge($ret, parent::gridColumns());
        return $ret;
    }

    public function componentToString(Kwf_Component_Data $data)
    {
        return $data->getChildComponent('-linktext')->getComponent()->getRow()->content;
    }
}
