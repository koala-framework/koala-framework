<?php
class Kwc_Basic_Line_Component extends Kwc_Abstract
{
    public static function getSettings($param = null)
    {
        $ret = array_merge(parent::getSettings($param), array(
            'componentName' => trlKwfStatic('Line')
        ));
        $ret['componentCategory'] = 'content';
        $ret['componentPriority'] = 65;
        $ret['apiContent'] = 'Kwc_Basic_Line_ApiContent';
        $ret['apiContentType'] = 'line';
        return $ret;
    }
}
