<?php
class Vpc_Events_Directory_Component extends Vpc_News_Directory_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Events.Events');
        $ret['childModel'] = 'Vpc_Events_Directory_Model';

        $ret['generators']['detail']['class'] = 'Vpc_News_Directory_Generator';
        $ret['generators']['detail']['component'] = 'Vpc_Events_Detail_Component';
        $ret['generators']['detail']['dbIdShortcut'] = 'events_';

        $ret['generators']['child']['component']['view'] = 'Vpc_Events_List_View_Component';

        return $ret;
    }

    public function getSelect()
    {
        $select = parent::getSelect();
        $select->where("IF(ISNULL(end_date), start_date, end_date) >= NOW()");
        return $select;
    }
}
