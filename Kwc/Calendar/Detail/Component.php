<?php
class Kwc_Calendar_Detail_Component extends Kwc_Directories_Item_Detail_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['contentSender'] = 'Kwf_Component_Abstract_ContentSender_Lightbox';
        $ret['cssClass'] = 'webStandard';
        $ret['contentWidth'] = 300;
        $ret['lightboxOptions']['cssClass'] = 'calendarDetail';
        return $ret;
    }
}
