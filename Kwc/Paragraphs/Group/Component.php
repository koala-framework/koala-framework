<?php
class Kwc_Paragraphs_Group_Component extends Kwc_Paragraphs_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['componentName'] = trlKwfStatic('Paragraphs Group');
        $ret['componentCategory'] = 'layout';
        $ret['componentPriority'] = 80;
        return $ret;
    }
}
