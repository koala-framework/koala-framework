<?php
class Kwc_Composite_TwoColumns_Left_Component extends Kwc_Paragraphs_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwf('Left column');
        return $ret;
    }
}
