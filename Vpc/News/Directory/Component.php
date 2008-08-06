<?php
class Vpc_News_Directory_Component extends Vpc_Directories_ItemPage_Directory_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('News.List');
        $ret['componentIcon'] = new Vps_Asset('newspaper');
        $ret['tablename'] = 'Vpc_News_Directory_Model';
        $ret['generators']['detail']['component'] = 'Vpc_News_Detail_Component';
        $ret['generators']['detail']['nameColumn'] = 'title';
        $ret['generators']['detail']['dbIdShortcut'] = 'news_';
        $ret['assetsAdmin']['files'][] = 'vps/Vpc/News/Directory/Panel.js';
        $ret['enableExpireDate'] = false;
        $ret['order'] = 'publish_date DESC';
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
