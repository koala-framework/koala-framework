<?php
class Vpc_News_Directory_Component extends Vpc_Directories_ItemPage_Directory_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('News.News');
        $ret['componentIcon'] = new Vps_Asset('newspaper');
        $ret['tablename'] = 'Vpc_News_Directory_Model';

        $ret['generators']['detail']['component'] = 'Vpc_News_Detail_Component';
        $ret['generators']['detail']['nameColumn'] = 'title';
        $ret['generators']['detail']['dbIdShortcut'] = 'news_';

        $ret['generators']['feed'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vpc_News_List_Feed_Component',
            'name' => trlVps('Feed')
        );

        $ret['assetsAdmin']['files'][] = 'vps/Vpc/News/Directory/Panel.js';
        $ret['enableExpireDate'] = false;
        $ret['order'] = array('field'=>'publish_date', 'direction'=>'DESC');
        $ret['assetsAdmin']['dep'][] = 'ExtFormDateField';
        return $ret;
    }

    public function getSelect()
    {
        $select = parent::getSelect();
        $select->where('publish_date <= NOW()');
        if ($this->_getSetting('enableExpireDate')) {
            $select->where('expiry_date >= NOW() OR ISNULL(expiry_date)');
        }
        return $select;
    }
}
