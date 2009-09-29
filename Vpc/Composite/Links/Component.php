<?php
class Vpc_Composite_Links_Component extends Vpc_Abstract_List_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component'] = 'Vpc_Basic_Link_Component';
        $ret['componentName'] = trlVps('Links');
        $ret['componentIcon'] = new Vps_Asset('links');
        $ret['childModel'] = 'Vpc_Composite_Links_Model';
        $ret['cssClass'] = 'webStandard';

        return $ret;
    }
}
