<?php
class Kwf_Component_Fulltext_BasicHtml_Html_Component extends Kwc_Basic_Html_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Kwf_Component_Fulltext_BasicHtml_Html_TestModel';
        return $ret;
    }
}
