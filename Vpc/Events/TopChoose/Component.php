<?php
class Vpc_Events_TopChoose_Component extends Vpc_Directories_TopChoose_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Events.Top');
        $ret['componentIcon'] = new Vps_Asset('newspaper');
        $ret['showDirectoryClass'] = 'Vpc_Events_Directory_Component';
        $ret['generators']['child']['component']['view'] = 'Vpc_Events_List_View_Component';
        $ret['order'] = array('field'=>'start_date', 'direction'=>'ASC');
        return $ret;
    }
}
