<?php
class Kwc_Events_TopChoose_Component extends Kwc_News_TopChoose_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('Events.Top');
        $ret['componentIcon'] = 'date';
        $ret['showDirectoryClass'] = 'Kwc_Events_Directory_Component';
        $ret['generators']['child']['component']['view'] = 'Kwc_Events_List_View_Component';
        return $ret;
    }
}
