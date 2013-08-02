<?php
class Kwc_News_Directory_Component extends Kwc_Directories_ItemPage_Directory_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('News.News');
        $ret['componentNameShort'] = trlKwfStatic('News');
        $ret['componentIcon'] = new Kwf_Asset('newspaper');
        $ret['childModel'] = 'Kwc_News_Directory_Model';

        $ret['generators']['detail']['class'] = 'Kwc_News_Directory_Generator';
        $ret['generators']['detail']['component'] = 'Kwc_News_Detail_Component';
        $ret['generators']['detail']['nameColumn'] = 'title';
        $ret['generators']['detail']['dbIdShortcut'] = 'news_';

        $ret['generators']['child']['component']['view'] = 'Kwc_News_List_View_Component';

        $ret['generators']['feed'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_News_List_Feed_Component',
            'name' => trlKwfStatic('Feed')
        );

        $ret['enableExpireDate'] = false;
        $ret['assetsAdmin']['dep'][] = 'ExtFormDateField';

        $ret['menuConfig'] = 'Kwf_Component_Abstract_MenuConfig_SameClass';

        //darf im seitenbaum nicht berbeitet werden
        $ret['extConfig'] = 'Kwf_Component_Abstract_ExtConfig_None';

        //config fuer admin button oben
        $ret['extConfigControllerIndex'] = 'Kwc_Directories_Item_Directory_ExtConfigEditButtons';

        return $ret;
    }

    public static function getViewCacheLifetimeForView()
    {
        return mktime(0, 0, 0, date('m'), date('d')+1, date('Y')) - time();
    }
}
