<?php
class Kwc_Composite_TwoColumns_Left_Component extends Kwc_Paragraphs_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['componentName'] = trlKwfStatic('Left column');
        return $ret;
    }
}
