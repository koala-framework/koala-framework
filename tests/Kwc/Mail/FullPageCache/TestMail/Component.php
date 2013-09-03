<?php
class Kwc_Mail_FullPageCache_TestMail_Component extends Kwc_Mail_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Kwc_Mail_FullPageCache_TestMail_Model';
        $ret['generators']['content']['component'] = 'Kwc_Mail_FullPageCache_TestMail_Html_Component';
        return $ret;
    }
}
