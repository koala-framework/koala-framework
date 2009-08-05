<?php
class Vpc_Newsletter_Unsubscribe_Success_Component extends Vpc_Form_Success_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['placeholder']['success'] = trlVps('You have been successfully unsubscribed from the newsletter.');
        return $ret;
    }
}
