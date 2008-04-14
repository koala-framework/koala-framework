<?php
class Vpc_Forum_Posts_Post_Component extends Vpc_Posts_Post_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['childComponentClasses']['user'] = 'Vpc_Forum_Posts_Post_UserDetail_Component';
        return $ret;
    }

    public function getForumComponent()
    {
        return $this->getParentComponent()->getForumComponent();
    }

    public function getGroupComponent()
    {
        return $this->getParentComponent()->getGroupComponent();
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $post = $this->getTable()->find($this->getCurrentComponentKey())->current();

        $ret['signature'] = '';
        $ret['avatarUrl'] = '';
        $ret['userUrl']   = '';

        $forumUserModel = new Vpc_Forum_User_Model();
        $user = $forumUserModel->find($post->user_id)->current();
        if ($user) {
            $userViewComponent = $this->getForumComponent()->getUserViewComponent($user);
            if ($userViewComponent) $ret['userUrl'] = $userViewComponent->getUrl();

            $ret['signature'] = $user->signature;

            if ($user->avatar) {
                $ret['avatarUrl'] = $user->getFileUrl('Avatar', 'avatarsmall');
            }
        }

        $ret['writeUrl'] = $this->getParentComponent()->getPageFactory()->getChildPageById('write')->getUrl()
            .'?quote='.$this->getId();

        return $ret;
    }

    public function mayEditPost()
    {
        $ret = parent::mayEditPost();
        if (!$ret) {
            $authedUser = Zend_Registry::get('userModel')->getAuthedUser();
            if ($authedUser) {
                $t = new Vpc_Forum_ModeratorModel();
                $row = $t->fetchRow(array(
                    'user_id = ?' => $authedUser->id,
                    'group_id = ?' => $this->getGroupComponent()->getCurrentPageKey()
                ));
                if ($row) return true;
            }
        }
        return $ret;
    }
}
