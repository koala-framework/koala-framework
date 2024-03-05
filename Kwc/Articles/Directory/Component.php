<?php
class Kwc_Articles_Directory_Component extends Kwc_Directories_ItemPage_Directory_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['assetsAdmin']['files'][] = 'kwf/Kwc/Articles/Directory/Controller.js';
        $ret['assetsAdmin']['files'][] = 'kwf/Kwc/Articles/Directory/AuthorsPanel.js';
        $ret['generators']['child']['component']['view'] = 'Kwc_Articles_Directory_View_Component';
        $ret['generators']['detail']['class'] = 'Kwc_Articles_Directory_Generator';
        $ret['generators']['detail']['component'] = 'Kwc_Articles_Detail_Component';
        $ret['generators']['detail']['dbIdShortcut'] = 'article_';
        $ret['showViewsController'] = false;

        $ret['childModel'] = 'Kwc_Articles_Directory_Model';

        //not allowed to process in pageTree
        $ret['extConfig'] = 'Kwf_Component_Abstract_ExtConfig_None';

        //config for admin button above
        $ret['extConfigControllerIndex'] = 'Kwc_Directories_Item_Directory_ExtConfigTabs';

        $ret['menuConfig'] = 'Kwc_Articles_Directory_MenuConfig';

        $ret['componentName'] = trlKwfStatic('Articles');
        $ret['componentIcon'] = 'newspaper';
        $ret['componentCategory'] = 'admin';

        $ret['contentSender'] = 'Kwc_Articles_Directory_ContentSender';

        return $ret;
    }

    public function getSelect()
    {
        $ret = parent::getSelect();
        $user = Kwf_Registry::get('userModel')->getAuthedUser();
        if (!$user || $user->role == 'external') {
            $ret->whereEquals('only_intern', 0);
        }
        $ret->order('date', 'DESC');
        return $ret;
    }
}
