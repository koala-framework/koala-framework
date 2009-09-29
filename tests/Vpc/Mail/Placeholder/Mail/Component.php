<?php
class Vpc_Mail_Placeholder_Mail_Component extends Vpc_Mail_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['content']['component'] = 'Vpc_Mail_Placeholder_Content_Component';
        $ret['ownModel'] = 'Vpc_Mail_Placeholder_Mail_Model';
        $ret['recipientSources']['test'] = 'Vpc_Mail_Placeholder_Mail_Recipients';
        return $ret;
    }
}
