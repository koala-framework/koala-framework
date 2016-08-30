<?php
class Kwc_Composite_TwoColumns_Right_Component extends Kwc_Paragraphs_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['componentName'] = trlKwfStatic('Right column');
        return $ret;
    }
}
