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

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $post = $this->getTable()->find($this->getCurrentComponentKey())->current();
        $forumUserModel = new Vpc_Forum_User_Model();
        $user = $forumUserModel->find($post->user_id)->current();
        if ($user) {
            $ret['signature'] = $user->signature;
        } else {
            $ret['signature'] = '';
        }
        return $ret;
    }
}
