<?php
class Vpc_Events_Directory_Component extends Vpc_News_Directory_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Events.Events');
        $ret['childModel'] = 'Vpc_Events_Directory_Model';

        $ret['generators']['detail']['component'] = 'Vpc_Events_Detail_Component';
        $ret['generators']['detail']['dbIdShortcut'] = 'events_';

        return $ret;
    }
    public function getSelect($overrideValues = array())
    {
        $select = Vpc_Directories_ItemPage_Directory_Component::getSelect();
        $date = isset($overrideValues['date']) ? $overrideValues['date'] : 'NOW()';
        $select->where("IF(ISNULL(end_date), start_date, end_date) >= {$date}");
        $select->order('start_date', 'ASC');
        return $select;
    }
}
