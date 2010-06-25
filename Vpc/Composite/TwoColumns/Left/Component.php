<?php
class Vpc_Composite_TwoColumns_Left_Component extends Vpc_Paragraphs_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Left column');
        return $ret;
    }
}
