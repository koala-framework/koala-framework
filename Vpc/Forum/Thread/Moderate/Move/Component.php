<?php
class Vpc_Forum_Thread_Moderate_Move_Component extends Vpc_Abstract_Composite_Component 
{
    public $newThread;
    
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['success'] = 'Vpc_Forum_Thread_Moderate_Move_Success_Component';
        $ret['viewCache'] = false;
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();

        $thread = $this->getData()->getParentPage();
        $group = $thread->getParentPage();
        $forum = $group->parent;

        if (!$group->getComponent()->mayModerate()) {
            throw new Vpc_AccessDeniedException();
        }

        //nicht in processInput bzw. postProcessInput weil wir 1. uns selbst brauchen zur anzeige
        //und 2. den neuen thread brauchen zum hinlinken
        $ret['moved'] = false;
        if ($this->_getParam('to')) {

            // Thread verschieben
            $componentId = $forum->getChildComponent('_' . $this->_getParam('to'))->dbId;
            $thread->row->component_id = $componentId;
            $thread->row->save();

            // Posts verschieben
            $newThreadId = $componentId . '_' . $thread->row->id;
            $componentId = $newThreadId . '-posts';
            foreach ($thread->getChildComponent('-posts')->getChildComponents(array('generator' => 'detail')) as $post) {
                $post->row->component_id = $componentId;
                $post->row->save();
            }
            $this->newThread = Vps_Component_Data_Root::getInstance()->getComponentById($newThreadId);
            $ret['moved'] = true;
        }

        $ret['groups'] = array();
        if (!$ret['moved']) {
            $ret['groups'] = $group->parent->getComponent()->getGroups();
        }
        $ret['groupsTemplate'] = Vpc_Admin::getComponentFile(get_class($this), 'Groups', 'tpl');
        $ret['threadTitle'] = $thread->row->subject;
        return $ret;
    }
}