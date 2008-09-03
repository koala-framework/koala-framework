<?php
class Vpc_Forum_Thread_Component extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['breadCrumbs'] = 'Vpc_Menu_BreadCrumbs_Component';
        $ret['generators']['child']['component']['posts'] = 'Vpc_Forum_Posts_Directory_Component';
        $ret['name'] = trlVps('Forum');
        return $ret;
    }
    
    public function getThreadVars()
    {
        $postsData = $this->getData()->getChildComponent('-posts');
        $select = $postsData->getGenerator('detail')->select($this->getData());
        
        $select->limit(1);
        $select->order('create_time', 'ASC');
        $firstPost = $postsData->getChildComponent($select);
        if ($firstPost) {
            $firstPost->user = Vps_Component_Data_Root::getInstance()
                ->getComponentByClass('Vpc_User_Directory_Component')
                ->getChildComponent('_' . $firstPost->row->user_id);
        }

        $select->unsetPart(Vps_Component_Select::ORDER);
        $select->order('create_time', 'DESC');
        $lastPost = $postsData->getChildComponent($select);
        if ($lastPost) {
            $lastPost->user = Vps_Component_Data_Root::getInstance()
                ->getComponentByClass('Vpc_User_Directory_Component')
                ->getChildComponent('_' . $lastPost->row->user_id);
        }
        
        $replies = $postsData->countChildComponents($select) - 1;
        
        $ret = array();
        $ret['replies'] = $replies;
        $ret['firstPost'] = $firstPost;
        $ret['lastPost'] = $lastPost;
        return $ret;
    }
}
