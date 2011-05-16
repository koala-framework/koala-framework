<?php
class Vpc_News_Directory_Component extends Vpc_Directories_ItemPage_Directory_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('News.News');
        $ret['componentIcon'] = new Vps_Asset('newspaper');
        $ret['childModel'] = 'Vpc_News_Directory_Model';

        $ret['generators']['detail']['class'] = 'Vpc_News_Directory_Generator';
        $ret['generators']['detail']['component'] = 'Vpc_News_Detail_Component';
        $ret['generators']['detail']['nameColumn'] = 'title';
        $ret['generators']['detail']['dbIdShortcut'] = 'news_';

        $ret['generators']['child']['component']['view'] = 'Vpc_News_List_View_Component';

        $ret['generators']['feed'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vpc_News_List_Feed_Component',
            'name' => trlVps('Feed')
        );

        $ret['enableExpireDate'] = false;
        $ret['assetsAdmin']['dep'][] = 'ExtFormDateField';
        $ret['flags']['hasResources'] = true;

        //darf im seitenbaum nicht berbeitet werden
        $ret['extConfig'] = 'Vps_Component_Abstract_ExtConfig_None';

        //config fuer admin button oben
        $ret['extConfigControllerIndex'] = 'Vpc_Directories_Item_Directory_ExtConfigEditButtons';

        return $ret;
    }

    public static function getViewCacheLifetimeForView()
    {
        return mktime(0, 0, 0, date('m'), date('d')+1, date('Y')) - time();
    }
}
