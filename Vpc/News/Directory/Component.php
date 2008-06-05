<?php
class Vpc_News_Directory_Component extends Vpc_News_List_Abstract_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = 'News.List';
        $ret['componentIcon'] = new Vps_Asset('newspaper');
        $ret['tablename'] = 'Vpc_News_Directory_Model';
        $ret['childComponentClasses']['detail'] = 'Vpc_News_Detail_Component';
        $ret['assetsAdmin']['files'][] = 'vps/Vpc/News/Directory/Panel.js';
        return $ret;
    }
    public function getNewsComponent()
    {
        return $this;
    }
}
