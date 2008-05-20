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

        $ret['rating'] = 0;
        if ($forumUser) {
            if ($forumUser->nickname) {
                $ret['name'] = $forumUser->nickname;
            }
            $ret['rating'] = $forumUser->getRating();
        }

        if ($user) {
            $page = $this->getForumComponent()->getUserViewComponent($forumUser);
            $ret['url'] = $page->getUrl();
        } else {
            $ret['url'] = null;
        }

        $row = false;
        if (!($this->getParentComponent() instanceof Vpc_Forum_User_View_Guestbook_Post_Component)) {
            $groupId = $this->getParentComponent()->getGroupComponent()->getCurrentPageKey();

            $t = new Vpc_Forum_ModeratorModel();
            $row = $t->fetchRow(array(
                'user_id = ?' => $user->id,
                'group_id = ?' => $groupId
            ));
        }

        $ret['isModerator'] = $row ? true : false;
        return $ret;
    }
    public function getForumComponent()
    {
        return $this->getParentComponent()->getParentComponent()->getForumComponent();
    }
}
