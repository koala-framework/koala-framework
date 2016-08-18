<?php
class Kwc_Guestbook_DeactivatePost_Component extends Kwc_Guestbook_ActivatePost_Component
{
    protected $_newVisibleValue = 0;

    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['placeholder']['success'] = trlKwfStatic('The entry in your guestbook has been deacitvated.');
        return $ret;
    }
}
