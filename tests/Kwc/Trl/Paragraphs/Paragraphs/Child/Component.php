<?php
class Kwc_Trl_Paragraphs_Paragraphs_Child_Component extends Kwc_Abstract
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['componentName'] = 'Child';
        return $ret;
    }
}
