<?php
abstract class Vpc_Events_Top_Component extends Vpc_News_Top_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Events.Top');
        $ret['generators']['child']['component']['view'] = 'Vpc_Events_List_View_Component';
        return $ret;
    }
}
