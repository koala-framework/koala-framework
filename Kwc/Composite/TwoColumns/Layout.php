<?php
class Kwc_Composite_TwoColumns_Layout extends Kwf_Component_Layout_Default
{
    public function getChildContentWidth(Kwf_Component_Data $data, Kwf_Component_Data $child)
    {
        $ret = parent::getChildContentWidth($data, $child);
        $ret -= $this->_getSetting('contentMargin') * 1;
        $ret = $ret / 2;
        return $ret;

    }
}
