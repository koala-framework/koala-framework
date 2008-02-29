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

        $t = new Vpc_Forum_Thread_Model();
        $thread = $t->find($this->getCurrentPageKey())->current();
        $ret = array();
        $ret['url'] = $this->getUrl();
        $ret['subject'] = $thread->subject;

        $user = Zend_Registry::get('userModel')->find($thread->user_id)->current();
        if ($user) {
            $ret['threadUser'] = $user->__toString();
            $page = $this->getForumComponent()->getUserViewComponent($user);
            $ret['threadUserUrl'] = $page->getUrl();
        } else {
            $ret['threadUser'] = 'Anonym';
            $ret['threadUserUrl'] = null;
        }
        $ret['threadTime'] = $thread->create_time;

        $posts = $this->getChildComponent('posts');

        $post = $posts->getTable()->getLastPost($posts->getDbId());
        $user = Zend_Registry::get('userModel')->find($post->user_id)->current();
        if ($user) {
            $ret['postUser'] = $user->__toString();
            $page = $this->getForumComponent()->getUserViewComponent($user);
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
