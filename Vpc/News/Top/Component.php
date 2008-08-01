<?php
class Vpc_News_Top_Component extends Vpc_Directories_Top_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('News.Top');
        $ret['componentIcon'] = new Vps_Asset('newspaper');
        $ret['showDirectoryClass'] = 'Vpc_News_Directory_Component';
        $ret['generators']['child']['component']['view'] = 'Vpc_News_List_Abstract_View_Component';
        return $ret;
    }
}
