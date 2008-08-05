<?php
class Vpc_Posts_Directory_View_Component extends Vpc_Directories_List_View_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['write'] = $this->getData()->parent->getChildComponent('_write');
        $ret['report'] = $this->getData()->parent->getChildComponent('_report');
        return $ret;
    }

}
