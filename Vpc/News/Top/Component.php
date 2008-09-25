<?php
abstract class Vpc_News_Top_Component extends Vpc_Directories_Top_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('News.Top');
        $ret['componentIcon'] = new Vps_Asset('newspaper');
        $ret['generators']['child']['component']['view'] = 'Vpc_News_List_View_Component';
        return $ret;
    }
}
