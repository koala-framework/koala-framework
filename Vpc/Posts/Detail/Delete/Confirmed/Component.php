<?php
class Vpc_Posts_Detail_Delete_Confirmed_Component extends Vpc_Posts_Success_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['placeholder']['success'] = trlVps('Comment was successfully deleted.');
        $ret['flags']['viewCache'] = false;
        return $ret;
    }
    protected function _getTargetPage()
    {
        return $this->getData()->getParentPage()->getParentPage();
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();

        //nicht in processInput löschen, da wir uns darin alles
        //unterm arsch weglöschen würden
        if ($this->getData()->parent->parent->getComponent()->mayEditPost()) {
            $row = $this->getData()->parent->parent->row;
            $row->delete();
        }
        return $ret;
    }
}
