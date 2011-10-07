<?php
class Vpc_Events_TopChoose_Component extends Vpc_News_TopChoose_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Events.Top');
        $ret['componentIcon'] = new Vps_Asset('date');
        $ret['showDirectoryClass'] = 'Vpc_Events_Directory_Component';
        $ret['generators']['child']['component']['view'] = 'Vpc_Events_List_View_Component';
        return $ret;
    }
}
