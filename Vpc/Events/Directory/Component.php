<?php
class Vpc_Events_Directory_Component extends Vpc_News_Directory_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Events.Events');
        $ret['tablename'] = 'Vpc_Events_Directory_Model';

        $ret['generators']['detail']['component'] = 'Vpc_Events_Detail_Component';
        $ret['generators']['detail']['dbIdShortcut'] = 'events_';

        $ret['order'] = null; //array('field'=>'start_date', 'direction'=>'DESC');
        return $ret;
    }
    public function getSelect()
    {
        $select = Vpc_Directories_ItemPage_Directory_Component::getSelect();
        $select->where('start_date >= NOW()');
        return $select;
    }
}
