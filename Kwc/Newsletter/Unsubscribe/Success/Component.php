<?php
class Kwc_Newsletter_Unsubscribe_Success_Component extends Kwc_Form_Success_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['placeholder']['success'] = trlKwf('You have been successfully unsubscribed from the newsletter.');
        return $ret;
    }
}
