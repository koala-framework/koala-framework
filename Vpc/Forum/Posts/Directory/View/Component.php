<?php
class Vpc_Forum_Posts_Directory_View_Component extends Vpc_Posts_Directory_View_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['observe'] = 'Vpc_Forum_Thread_Observe_Component';
        $ret['generators']['child']['component']['moderate'] = 'Vpc_Forum_Thread_Moderate_Component';
        return $ret;
    }
    
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['threadClosed'] = $this->getData()->getChildComponent('-moderate')->
            getChildComponent('-close')->getComponent()->isClosed();
        $ret['mayModerate'] = $this->getData()->getParentPage()->getComponent()->mayModerate();
        
        return $ret;
    }
}
