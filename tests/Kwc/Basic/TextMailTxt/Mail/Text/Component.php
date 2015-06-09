<?php
class Kwc_Basic_TextMailTxt_Mail_Text_Component extends Kwc_Basic_Text_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['link'] = 'Kwc_Basic_TextMailTxt_Mail_Text_Link_Component';
        $ret['ownModel'] = 'Kwc_Basic_TextMailTxt_Mail_Text_TestModel';
        return $ret;
    }
}
