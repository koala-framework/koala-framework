<?php
class Vpc_Mail_Mail extends Vpc_Mail_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['mail']['component'] = array(
            'mail' => 'Vpc_Mail_Mail_Component'
        );
        $ret['modelname'] = 'Vpc_Mail_MailModel';
        return $ret;
    }
}
