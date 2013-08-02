<?php
class Kwc_Articles_Directory_Component extends Kwc_Directories_ItemPage_Directory_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['assetsAdmin']['files'][] = 'kwf/Kwc/Articles/Directory/Controller.js';
        $ret['generators']['child']['component']['view'] = 'Kwc_Articles_Directory_View_Component';
        $ret['generators']['detail']['class'] = 'Kwc_Articles_Directory_Generator';
        $ret['generators']['detail']['component'] = 'Kwc_Articles_Detail_Component';
        $ret['generators']['detail']['dbIdShortcut'] = 'article_';

        $ret['childModel'] = 'Kwc_Articles_Directory_Model';

        //not allowed to process in pageTree
        $ret['extConfig'] = 'Kwf_Component_Abstract_ExtConfig_None';

        //config for admin button above
        $ret['extConfigControllerIndex'] = 'Kwc_Directories_Item_Directory_ExtConfigTabs';

        $ret['menuConfig'] = 'Kwc_Articles_Directory_MenuConfig';

        $ret['componentNameShort'] = trlKwfStatic('Articles');
        $ret['componentName'] = trlKwfStatic('Articles') . '.' . trlKwfStatic('Articles');
        $ret['componentIcon'] = new Kwf_Asset('newspaper');

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
