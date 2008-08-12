<?php
class Vpc_Forum_Thread_Directory_View_Component extends Vpc_Posts_Directory_View_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['observe'] = 'Vpc_Forum_Thread_Observe_Component';
        return $ret;
    }
    
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $postNumber = 1;
        foreach ($ret['items'] as &$item) {
            $item->postNumber = $postNumber++;
        }
        return $ret;
    }
}
