<?php
class Vpc_NewsletterCategory_Detail_Mail_Component extends Vpc_Newsletter_Detail_Mail_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['recipientSources'] = array(
            'n' => 'Vpc_NewsletterCategory_Subscribe_Model'
        );
        return $ret;
    }
}