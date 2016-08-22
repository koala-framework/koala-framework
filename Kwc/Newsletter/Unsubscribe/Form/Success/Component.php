<?php
class Kwc_Newsletter_Unsubscribe_Form_Success_Component extends Kwc_Form_Success_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['placeholder']['success'] = trlKwfStatic('You have been successfully unsubscribed from the newsletter.');
        return $ret;
    }
}
