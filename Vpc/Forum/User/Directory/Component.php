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
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        return $ret;
    }
    
}