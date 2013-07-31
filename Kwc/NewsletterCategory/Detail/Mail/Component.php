<?php
class Kwc_NewsletterCategory_Detail_Mail_Component extends Kwc_Newsletter_Detail_Mail_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['recipientSources'] = array(
            'n' => array(
                'model' => 'Kwc_NewsletterCategory_Subscribe_Model'
            )
        );
        return $ret;
    }
}
