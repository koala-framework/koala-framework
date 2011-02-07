<?php
class Vpc_News_Directory_Component extends Vpc_Directories_ItemPage_Directory_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('News.News');
        $ret['componentIcon'] = new Vps_Asset('newspaper');
        $ret['childModel'] = 'Vpc_News_Directory_Model';

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
        return $ret;
    }

    public function getSelect()
    {
        $select = parent::getSelect();
        $select->where('publish_date <= NOW()');
        if ($this->_getSetting('enableExpireDate')) {
            $select->where('expiry_date >= NOW() OR ISNULL(expiry_date)');
        }
        $select->order('publish_date', 'DESC');
        return $select;
    }

    public static function getViewCacheLifetimeForView()
    {
        return mktime(0, 0, 0, date('m'), date('d')+1, date('Y')) - time();
    }
}
