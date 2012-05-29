<?php
class Kwc_Paragraphs_Group_Component extends Kwc_Paragraphs_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('Paragraphs Group');
        return $ret;
    }
}
