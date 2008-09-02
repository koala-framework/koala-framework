<?php
class Vpc_Posts_Post_Delete_Confirmed_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['cssClass'] = 'webStandard webSuccess';
        return $ret;
    }
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        if ($this->getData()->parent->parent->getComponent()->mayEditPost()) {
            $row = $this->getData()->parent->parent->row;
            $row->delete();
        }
        return $ret;
    }
}
