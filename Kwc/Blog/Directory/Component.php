<?php
class Kwc_Blog_Directory_Component extends Kwc_Directories_ItemPage_Directory_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['componentName'] = trlKwfStatic('Blog');
        $ret['componentIcon'] = 'newspaper';
        $ret['componentCategory'] = 'admin';
        $ret['childModel'] = 'Kwc_Blog_Directory_Model';
        $ret['ownModel'] = 'Kwf_Component_FieldModel';

        $ret['generators']['detail']['class'] = 'Kwc_Blog_Directory_Generator';
        $ret['generators']['detail']['component'] = 'Kwc_Blog_Detail_Component';
        $ret['generators']['detail']['nameColumn'] = 'title';
        $ret['generators']['detail']['dbIdShortcut'] = 'blog_';

        $ret['generators']['child']['component']['view'] = 'Kwc_Blog_List_View_Component';

        $ret['generators']['feed'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Blog_List_Feed_Component',
            'name' => trlKwfStatic('Feed')
        );
        $ret['generators']['categories'] = array(
            'class' => 'Kwf_Component_Generator_PseudoPage_Static',
            'component' => 'Kwc_Blog_Category_Directory_Component',
            'name' => trlKwfStatic('Categories'),
            'showInMenu' => false
        );
        $ret['assetsAdmin']['dep'][] = 'ExtFormDateField';

        $ret['menuConfig'] = 'Kwf_Component_Abstract_MenuConfig_SameClass';

        //darf im seitenbaum nicht berbeitet werden
        $ret['extConfig'] = 'Kwc_Blog_Directory_ExtConfig';

        //config fuer admin button oben
        $ret['extConfigControllerIndex'] = 'Kwc_Directories_Item_Directory_ExtConfigTabs';

        return $ret;
    }

    public static function getViewCacheLifetimeForView()
    {
        return mktime(0, 0, 0, date('m'), date('d')+1, date('Y')) - time();
    }
}
