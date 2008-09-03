<?php
class Vpc_Posts_Post_Delete_Confirmed_Component extends Vpc_Posts_Success_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['cssClass'] = 'webStandard webSuccess';
        $ret['placeholder']['success'] = trlVps('Comment was successfully deleted.');
        return $ret;
    }
    protected function _getTargetPage()
    {
        return $this->getData()->getParentPage()->getParentPage();
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
