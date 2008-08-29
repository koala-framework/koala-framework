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
        $select = $this->getData()->getGenerator('detail')->select($this->getData());
        
        $select->limit(1);
        $select->order('create_time ASC');
        $firstPost = $this->getData()->getChildComponent($select);
        if ($firstPost) {
            $firstPost->user = Vps_Component_Data_Root::getInstance()
                ->getComponentByClass('Vpc_User_Directory_Component')
                ->getChildComponent('_' . $firstPost->row->user_id);
        }
        
        $select->order('create_time DESC');
        $lastPost = $this->getData()->getChildComponent($select);
        if ($lastPost) {
            $lastPost->user = Vps_Component_Data_Root::getInstance()
                ->getComponentByClass('Vpc_User_Directory_Component')
                ->getChildComponent('_' . $lastPost->row->user_id);
        }
        
        $select->setIntegrityCheck(false);
        $select->reset(Zend_Db_Select::COLUMNS);
        $select->from(null, array('count' => "COUNT(*)"));
//         $replies = $select->query()->fetchColumn(0) - 1;
        $replies = '?';
        
        $ret = array();
        $ret['replies'] = $replies;
        $ret['firstPost'] = $firstPost;
        $ret['lastPost'] = $lastPost;
        return $ret;
    }
}
