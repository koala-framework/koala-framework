<?php
class Kwc_News_TopChoose_Component extends Kwc_Directories_TopChoose_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['componentName'] = trlKwfStatic('News.Top');
        $ret['componentIcon'] = 'newspaper';
        $ret['showDirectoryClass'] = 'Kwc_News_Directory_Component';
        $ret['generators']['child']['component']['view'] = 'Kwc_News_List_View_Component';
        return $ret;
    }
}
