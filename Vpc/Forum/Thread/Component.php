<?php
class Vpc_Forum_Thread_Component extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['posts'] = 'Vpc_Forum_Posts_Directory_Component';
        return $ret;
    }

    public function getThreadVars()
    {
        $data = $this->getData();

        ///$forumUserTable = new Vpc_Forum_User_Model();
        $ret = array();
        /*
        $t = new Vpc_Forum_Thread_Model();
        $thread = $t->find($this->getCurrentPageKey())->current();
        $ret = array();
        $ret['url'] = $this->getUrl();
        $ret['subject'] = htmlspecialchars($thread->subject);
        $ret['thread_id'] = $thread->id;
        $ret['threadClosed'] = $thread->closed;
        */
        $user = $data->row->findParentRow(get_class(Vps_Registry::get('userModel')));
        //d(Vps_Registry::get('userModel'));
        if ($user) {
            $ret['threadUser'] = $user->nickname != '' ? $user->nickname : $user->firstname;
            $ret['threadUserUrl'] = 'TODO';
        } else {
            $ret['threadUser'] = 'Anonym';
            $ret['threadUserUrl'] = null;
        }
        $ret['threadTime'] = $data->row->create_time;
        
        $posts = $data->getChildComponent('-posts')->getChildComponents(array('generator' => 'detail'));
        $ret['lastPost'] = array_pop($posts);
        $ret['replies'] = count($posts) - 1;
        d($ret);
        return $ret;
    }
}
