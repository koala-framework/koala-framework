<?php
class Vpc_Forum_Thread_Component extends Vpc_Abstract_Composite_Component
{
    private $_threadVars;

    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['posts'] = 'Vpc_Forum_Posts_Directory_Component';
        $ret['generators']['child']['component']['observe'] = 'Vpc_Forum_Thread_Observe_Component';
        $ret['generators']['child']['component']['moderate'] = 'Vpc_Forum_Thread_Moderate_Component';
        $ret['generators']['child']['component']['preview'] = 'Vpc_Forum_Thread_Preview_Component';
        $ret['generators']['edit'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vpc_Forum_Thread_Edit_Component',
            'name' => trlVps('Edit')
        );
        $ret['name'] = trlVps('Forum');
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['write'] = $this->getData()->getChildComponent('-posts')->getChildComponent('_write');
        $ret['threadClosed'] = $this->getData()->getChildComponent('-moderate')->
            getChildComponent('-close')->getComponent()->isClosed();
        $ret['mayModerate'] = $this->getData()->getParentPage()->getComponent()->mayModerate();
        return $ret;
    }

    public function getThreadVars()
    {
        if ($this->_threadVars) return $this->_threadVars;

        $postsData = $this->getData()->getChildComponent('-posts');
        $select = $postsData->getGenerator('detail')->select($this->getData());

        $select->limit(1);
        $select->order('create_time', 'ASC');
        $firstPost = $postsData->getChildComponent($select);
        if ($firstPost) {
            $firstPost->user = Vps_Component_Data_Root::getInstance()
                ->getComponentByClass(
                    'Vpc_User_Directory_Component',
                    array('subroot' => $this->getData())
                )
                ->getChildComponent('_' . $firstPost->row->user_id);
        }

        $select->unsetPart(Vps_Component_Select::ORDER);
        $select->order('create_time', 'DESC');
        $lastPost = $postsData->getChildComponent($select);
        if ($lastPost) {
            $lastPost->user = Vps_Component_Data_Root::getInstance()
                ->getComponentByClass(
                    'Vpc_User_Directory_Component',
                    array('subroot' => $this->getData())
                )
                ->getChildComponent('_' . $lastPost->row->user_id);
        }

        $replies = $postsData->countChildComponents($select) - 1;

        $ret = array();
        $ret['replies'] = $replies;
        $ret['firstPost'] = $firstPost;
        $ret['lastPost'] = $lastPost;
        $this->_threadVars = $ret;
        return $ret;
    }

    public static function getStaticCacheMeta($componentClass)
    {
        $ret = parent::getStaticCacheMeta($componentClass);
        $ret[] = new Vps_Component_Cache_Meta_Static_GeneratorRow();
        return $ret;
    }
}
