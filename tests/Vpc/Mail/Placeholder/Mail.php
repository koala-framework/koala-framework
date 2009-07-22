<?php
class Vpc_Mail_Placeholder_Mail extends Vpc_Mail_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['content']['component'] = 'Vpc_Mail_Placeholder_Content_Component';
        $ret['modelname'] = 'Vpc_Mail_Placeholder_MailModel';
        return $ret;
    }

    public function getPlaceholders(Vpc_Mail_Recipient_Interface $recipient = null)
    {
        $ret = parent::getPlaceholders($recipient);
        if ($recipient) {
            $text = 'Sehr ';
            $text .= $recipient->gender == 'male' ? 'geehrter Herr' : 'geehrte Frau';
            if ($recipient->title) $text .= ' ' . $recipient->title;
            $text .= ' ' . $recipient->lastname;
            $ret['sehr_geehrt'] = $text;
        }
        return $ret;
    }
}
