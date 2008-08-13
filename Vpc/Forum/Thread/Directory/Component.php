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
    
    public function getUserComponent($userId)
    {
        return $this->getData()->parent->parent->getChildComponent('_users')->getChildComponent('_' . $userId);
    }
}
