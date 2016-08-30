<?php
class Kwc_Basic_TextMailTxt_Mail_Text_Link_Component extends Kwc_Basic_LinkTag_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['child']['component'] = array(
            //only extern
            'extern' => 'Kwc_Basic_TextMailTxt_Mail_Text_Link_Extern_Component'
        );
        $ret['ownModel'] = 'Kwc_Basic_TextMailTxt_Mail_Text_Link_TestModel';
        return $ret;
    }
}
