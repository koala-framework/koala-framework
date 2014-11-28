<?php
class Kwc_Events_Directory_Component extends Kwc_News_Directory_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('Events.Events');
        $ret['componentNameShort'] = trlKwfStatic('Events');
        $ret['componentIcon'] = new Kwf_Asset('date');
        $ret['childModel'] = 'Kwc_Events_Directory_Model';

        $ret['generators']['detail']['class'] = 'Kwc_Events_Directory_Generator';
        $ret['generators']['detail']['component'] = 'Kwc_Events_Detail_Component';
        $ret['generators']['detail']['dbIdShortcut'] = 'events_';

        $ret['generators']['child']['component']['view'] = 'Kwc_Events_List_View_Component';

        return $ret;
    }

    public function getSelect()
    {
        $select = parent::getSelect();
        // nur aktuellen tag nehmen ohne uhrzeit, dann sieht man ein event
        // den restlichen tag, egal welche uhrzeit. - das soll so sein
        $select->where("IF(ISNULL(end_date), start_date, end_date) >= CURDATE()");
        return $select;
    }
}
