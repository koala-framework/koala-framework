<?php
class Vpc_Forum_Thread_Component extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = array_merge(parent::getSettings(), array(
            'childComponentClasses' => array(
                'posts'       => 'Vpc_Forum_Posts_Component',
                'movethread'  => 'Vpc_Forum_Thread_Move_Component'
            )
        ));
        return $ret;
    }

    public function getTemplateVars()
    {
        if ($this->_getParam('closeToggle')) {
            $t = new Vpc_Forum_Thread_Model();
            $threadRow = $t->find($this->getCurrentPageKey())->current();

            if ($threadRow->closed) {
                $threadRow->closed = 0;
            } else {
                $threadRow->closed = 1;
            }
            $threadRow->save();
        }

        $vars = parent::getTemplateVars();
        $vars['moveUrl'] = '';
        $vars['closeToggleUrl'] = '';
        $vars['closeToggleCurrent'] = 0;

        if ($this->getGroupComponent()->mayModerate()) {
            $moveComponent = $this->getPageFactory()->getChildPageById('movethread')
                ->getComponent();
            $vars['moveUrl'] = $moveComponent->getUrl();

            $t = new Vpc_Forum_Thread_Model();
            $thread = $t->find($this->getCurrentPageKey())->current();

            $vars['closeToggleUrl'] = $this->getUrl().'?closeToggle=1';
            $vars['closeToggleCurrent'] = $thread->closed;
        }

        return $vars;
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
        $ret['subject'] = htmlspecialchars($thread->subject);
        $ret['thread_id'] = $thread->id;
        $ret['threadClosed'] = $thread->closed;

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
        $ret['postUserAvatarUrl'] = '';
        if ($user) {
            if ($forumUser->nickname) {
                $ret['postUser'] = $forumUser->nickname;
            } else {
                $ret['postUser'] = $user->firstname;
            }

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
