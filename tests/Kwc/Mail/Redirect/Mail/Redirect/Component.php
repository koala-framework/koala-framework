<?php
class Kwc_Mail_Redirect_Mail_Redirect_Component extends Kwc_Mail_Redirect_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['childModel'] = 'Kwc_Mail_Redirect_Mail_Redirect_Model';
        return $ret;
    }
}
