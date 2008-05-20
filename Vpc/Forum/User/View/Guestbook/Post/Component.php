<?php
class Vpc_Forum_User_View_Guestbook_Post_Component extends Vpc_Forum_Posts_Post_Component
{
    public function mayModeratePost()
    {
        $moderatorRoles = $this->_getSetting('moderatorRoles');
        $authedUserRole = Zend_Registry::get('userModel')->getAuthedUserRole();

        if (!is_array($moderatorRoles)) {
            throw new Vps_Exception("The setting 'moderatorRoles' must be an array.");
        }
        if ($authedUserRole && in_array($authedUserRole, $moderatorRoles)) {
            return true;
        }

        $authedUser = Zend_Registry::get('userModel')->getAuthedUser();
        if ($authedUser && $authedUser->id == $this->getCurrentPageKey()) {
            return true;
        }
        return false;
    }

    public function mayEditPost()
    {
        return false;
    }

    public function getForumComponent()
    {
        return $this->getParentComponent()->getForumComponent();
    }

    public function getGroupComponent()
    {
        return null;
    }

    public function getThreadComponent()
    {
        return null;
    }
}