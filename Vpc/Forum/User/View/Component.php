<?php
class Vpc_Forum_User_View_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['tablename'] = 'Vpc_Forum_User_Model';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $userId = $this->getCurrentPageKey();

        $userData = Zend_Registry::get('userModel')->find($userId)->current();
        $forumUserData = $this->getTable()->find($userId)->current();

        $ret['userData'] = $userData->toArray();
        $ret['userPosts'] = $forumUserData->getNumPosts();
        $ret['userThreads'] = $forumUserData->getNumThreads();
        $ret['forumUserData'] = $forumUserData->toArray();

        return $ret;
    }
}