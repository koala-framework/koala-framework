<?php
class Vpc_Guestbook_DeactivatePost_Component extends Vpc_Guestbook_ActivatePost_Component
{
    protected $_newVisibleValue = 0;

    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['placeholder']['success'] = trlVpsStatic('The entry in your guestbook has been deacitvated.');
        return $ret;
    }
}
