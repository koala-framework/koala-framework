<?php
class Vpc_Forum_Thread_Directory_Component extends Vpc_Posts_Directory_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['view'] = 'Vpc_Forum_Thread_Directory_View_Component';
        $ret['name'] = trlVps('Forum');
        return $ret;
    }
    
    public function getThreadVars()
    {
        $posts = $this->getData()->getChildComponents(array('generator' => 'detail'));
        $ret = array();
        $ret['replies'] = count($posts) - 1;
        $ret['firstPost'] = array_shift($posts);
        $ret['lastPost'] = array_pop($posts);
        if (!$ret['lastPost']) $ret['lastPost'] = $ret['firstPost'];
        $ret['firstUser'] = $this->getData()->parent->parent->getChildComponent('_users')->getChildComponent('_' . $ret['firstPost']->row->user_id);
        $ret['lastUser'] = $this->getData()->parent->parent->getChildComponent('_users')->getChildComponent('_' . $ret['lastPost']->row->user_id);
        return $ret;
    }
    
    public function getUserComponent($userId)
    {
        return $this->getData()->parent->parent->getChildComponent('_users')->getChildComponent('_' . $userId);
    }
}
