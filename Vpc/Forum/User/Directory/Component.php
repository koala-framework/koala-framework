<?php
class Vpc_Forum_User_Directory_Component extends Vpc_User_Directory_Component  
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['view'] = 'Vpc_Forum_User_View_Component';
        $ret['generators']['detail']['component'] = 'Vpc_Forum_User_Detail_Component';
        $ret['generators']['detail']['filenameColumn'] = 'forumname';
        $ret['generators']['detail']['nameColumn'] = 'forumname';
        unset($ret['generators']['register']);
        unset($ret['generators']['edit']);
        unset($ret['generators']['login']);
        unset($ret['generators']['lostPassword']);
        unset($ret['generators']['activate']);
        return $ret;
    }
}