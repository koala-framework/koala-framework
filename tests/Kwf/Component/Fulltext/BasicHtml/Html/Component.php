<?php
class Kwf_Component_Fulltext_BasicHtml_Html_Component extends Kwc_Basic_Html_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['ownModel'] = 'Kwf_Component_Fulltext_BasicHtml_Html_TestModel';
        return $ret;
    }
}
