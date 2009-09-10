<?php
class Vpc_NewsletterCategory_Subscribe_Mail_Component extends Vpc_Newsletter_Subscribe_Mail_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['recipientSources']['sub'] = 'Vpc_NewsletterCategory_Subscribe_Model';
        return $ret;
    }
}
