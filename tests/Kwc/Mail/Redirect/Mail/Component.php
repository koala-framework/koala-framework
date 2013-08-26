<?php
class Kwc_Mail_Redirect_Mail_Component extends Kwc_Mail_Placeholder_Mail_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['content']['component'] = 'Kwc_Mail_Redirect_Content_Component';
        
        $ret['generators']['redirect']['component'] = 'Kwc_Mail_Redirect_Mail_Redirect_Component';
        return $ret;
    }
}
