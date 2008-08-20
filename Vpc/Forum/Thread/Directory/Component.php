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
        if ($ret['firstPost']) {
            $ret['firstPost']->user = Vps_Component_Data_Root::getInstance()
                            ->getComponentByClass('Vpc_User_Directory_Component')
                            ->getChildComponent('_' . $ret['firstPost']->row->user_id);
        } else {
        }
        if ($ret['lastPost']) {
            $ret['lastPost']->user = Vps_Component_Data_Root::getInstance()
                            ->getComponentByClass('Vpc_User_Directory_Component')
                            ->getChildComponent('_' . $ret['lastPost']->row->user_id);
        }
        return $ret;
    }
}
