<?php
class Kwc_Mail_Placeholder_Mail_Component extends Kwc_Mail_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['content']['component'] = 'Kwc_Mail_Placeholder_Content_Component';
        $ret['ownModel'] = 'Kwc_Mail_Placeholder_Mail_Model';
        $ret['recipientSources']['test'] = 'Kwc_Mail_Placeholder_Mail_Recipients';
        $ret['docType'] = false;
        return $ret;
    }
}
