<?php
class RedMallee_Box_FooterLogos_Component extends Kwc_Abstract_List_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component'] = 'RedMallee_Box_FooterLogos_Links_Component';
        $ret['componentName'] = trlKwfStatic('Logos for Footer');
        $ret['cssClass'] = 'webStandard';
        return $ret;
    }
}
