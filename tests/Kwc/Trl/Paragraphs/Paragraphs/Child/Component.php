<?php
class Kwc_Trl_Paragraphs_Paragraphs_Child_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = 'Child';
        return $ret;
    }
}
