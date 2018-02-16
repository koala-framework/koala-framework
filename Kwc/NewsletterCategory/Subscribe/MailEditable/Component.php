<?php
class Kwc_NewsletterCategory_Subscribe_MailEditable_Component extends Kwc_Newsletter_Subscribe_MailEditable_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['recipientSources']['sub'] = 'Kwc_NewsletterCategory_Subscribe_Model';
        return $ret;
    }
}
