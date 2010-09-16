<?php
class Vpc_Composite_TwoColumns_Right_Component extends Vpc_Paragraphs_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Right column');
        return $ret;
    }
}
