<?php
class Kwc_Mail_FullPageCache_TestMail_Html_Component extends Kwc_Basic_Html_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['ownModel'] = 'Kwc_Mail_FullPageCache_TestMail_Html_Model';
        return $ret;
    }
}
