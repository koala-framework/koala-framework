<?php
class Kwc_Mail_Redirect_Mail_Redirect_Component extends Kwc_Mail_Redirect_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['childModel'] = 'Kwc_Mail_Redirect_Mail_Redirect_Model';
        return $ret;
    }
}
