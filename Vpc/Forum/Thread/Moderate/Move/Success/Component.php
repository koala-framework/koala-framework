<?php
class Vpc_Forum_Thread_Moderate_Move_Success_Component extends Vpc_Posts_Success_Component
{
    protected function _getTargetPage()
    {
        return $this->getData()->parent->getComponent()->newThread;
    }
    
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['placeholder']['success'] = trlVps('Thread was successfully moved.');
        return $ret;
    }
}
