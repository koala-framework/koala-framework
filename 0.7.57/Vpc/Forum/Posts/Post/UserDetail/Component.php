<?php
class Vpc_Forum_Posts_Post_UserDetail_Component extends Vpc_Posts_Post_UserDetail_Component
{
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $user = $this->_getUser();
        $forumUser = null;
        if ($user) {
            $forumUserTable = new Vpc_Forum_User_Model();
            $forumUser = $forumUserTable->fetchRow(array('id = ?' => $user->id));
        }

        if ($forumUser) {
            if ($forumUser->nickname) {
                $ret['name'] = $forumUser->nickname;
            }
        }

        if ($user) {
            $page = $this->getForumComponent()->getUserViewComponent($forumUser);
            $ret['url'] = $page->getUrl();
        } else {
            $ret['url'] = null;
        }
        return $ret;
    }
    public function getForumComponent()
    {
        return $this->getParentComponent()->getForumComponent();
    }
}
