<?php
class Kwc_List_Links_Component extends Kwc_Abstract_List_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component'] = 'Kwc_Basic_Link_Component';
        $ret['componentName'] = trlKwfStatic('Links');
        $ret['componentIcon'] = new Kwf_Asset('links');
        $ret['cssClass'] = 'webStandard';
        return $ret;
    }
}
