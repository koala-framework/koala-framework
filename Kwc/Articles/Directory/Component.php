<?php
class Kwc_Articles_Directory_Component extends Kwc_Directories_ItemPage_Directory_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['view'] = 'Kwc_Articles_Directory_View_Component';

        $ret['childModel'] = 'Kwc_Articles_Directory_Model';

        //not allowed to process in pageTree
        $ret['extConfig'] = 'Kwf_Component_Abstract_ExtConfig_None';

        //config for admin button above
        $ret['extConfigControllerIndex'] = 'Kwc_Directories_Item_Directory_ExtConfigTabs';

        $ret['menuConfig'] = 'Kwc_Articles_Directory_MenuConfig';

        $ret['componentName'] = trlKwf('Articles');

        $ret['contentSender'] = 'Kwc_Articles_Directory_ContentSender';
        return $ret;
    }

    public function getSelect()
    {
        $ret = parent::getSelect();
        $ret->whereEquals('autheduser_visible', 1);
        return $ret;
    }
}
