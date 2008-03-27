<?php
class Vpc_Forum_Thread_Component extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = array_merge(parent::getSettings(), array(
            'childComponentClasses' => array(
                'posts'       => 'Vpc_Forum_Posts_Component',
            )
        ));
        return $ret;
    }
    public function getThreadVars()
    {
        $where = array();
        $where['component_id = ?'] = $this->getDbId();
        $ret['threads'] = array();

        $forumUserTable = new Vpc_Forum_User_Model();

        $t = new Vpc_Forum_Thread_Model();
        $thread = $t->find($this->getCurrentPageKey())->current();
        $ret = array();
        $ret['url'] = $this->getUrl();
        $ret['subject'] = $thread->subject;

        $forumUser = $forumUserTable->fetchRow(array('id = ?' => $thread->user_id));
        $user = Zend_Registry::get('userModel')->find($thread->user_id)->current();
        if ($user) {
            if ($forumUser->nickname) {
                $ret['threadUser'] = $forumUser->nickname;
            } else {
                $ret['threadUser'] = $user->firstname;
            }
            $page = $this->getForumComponent()->getUserViewComponent($forumUser);
            $ret['threadUserUrl'] = $page->getUrl();
        } else {
            $ret['threadUser'] = 'Anonym';
            $ret['threadUserUrl'] = null;
        }
        $ret['threadTime'] = $thread->create_time;

        $posts = $this->getChildComponent('posts');

        $post = $posts->getTable()->getLastPost($posts->getDbId());
        $forumUser = $forumUserTable->fetchRow(array('id = ?' => $post->user_id));
        $user = Zend_Registry::get('userModel')->find($post->user_id)->current();
        if ($user) {
            if ($forumUser->nickname) {
                $ret['postUser'] = $forumUser->nickname;
            } else {
                $ret['postUser'] = $user->firstname;
            }
            $ret['postUserAvatarUrl'] = '';
            if ($forumUser->avatar) {
                $ret['postUserAvatarUrl'] = $forumUser->getFileUrl('Avatar', 'avatarsmall');
            }
            $page = $this->getForumComponent()->getUserViewComponent($forumUser);
            $ret['postUserUrl'] = $page->getUrl();
        } else {
            $ret['postUser'] = 'Anonym';
            $ret['postUserUrl'] = null;
        }
        $ret['postTime'] = $post->create_time;

        $ret['replies'] = $posts->getTable()->getNumReplies($posts->getDbId());
        return $ret;
    }

    public function getGroupComponent()
    {
        return $this->getParentComponent();
    }
    public function getForumComponent()
    {
        return $this->getGroupComponent()->getForumComponent();
    }
}
