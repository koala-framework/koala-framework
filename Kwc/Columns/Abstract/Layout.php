<?php
class Kwc_Columns_Abstract_Layout extends Kwf_Component_Layout_Default
{
    public function getChildContentWidth(Kwf_Component_Data $data, Kwf_Component_Data $child)
    {
        $ownWidth = parent::getChildContentWidth($data, $child);
        $widthCalc = $child->row->col_span / $child->row->columns;
        $ret = floor($ownWidth * $widthCalc);
        if ($ret < 480) {
            $ret = min($ownWidth, 480);
        }
        return $ret;
    }
}
