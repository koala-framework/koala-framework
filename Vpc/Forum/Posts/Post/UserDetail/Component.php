<?php
class Vpc_Forum_Posts_Post_UserDetail_Component extends Vpc_Posts_Post_UserDetail_Component
{
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $user = $this->_getUser();
        if ($user) {
            $page = $this->getForumComponent()->getUserViewComponent($user);
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
