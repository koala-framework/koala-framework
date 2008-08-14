<?php
class Vpc_Forum_Thread_Moderate_Move_Component extends Vpc_Abstract
{
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();

        $ret['moved'] = false;
        $ret['groups'] = array();
        $thread = $this->getData()->getParentPage();
        $group = $thread->getParentPage();
        $forum = $group->parent;
        
        if ($group->getComponent()->mayModerate()) {
            if ($this->_getParam('to')) {
                // Thread verschieben
                $componentId = $forum->getChildComponent('_' . $this->_getParam('to'))->dbId;
                $thread->row->component_id = $componentId;
                $thread->row->save();

                // Posts verschieben
                $componentId .= '_' . $thread->id;
                foreach ($thread->getChildComponents(array('generator' => 'detail')) as $post) {
                    $post->row->component_id = $componentId;
                    $post->row->save();
                }

                $ret['moved'] = true;
            } else {
                $ret['groups'] = $forum->getComponent()->getGroups();
            }
        }

        $ret['groupsTemplate'] = Vpc_Admin::getComponentFile(get_class($this), 'Groups', 'tpl');
        $ret['threadTitle'] = $thread->row->subject;
        return $ret;
    }
}