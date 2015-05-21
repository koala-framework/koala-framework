<?php
class Kwc_Posts_Write_Form_Success_Component extends Kwc_Posts_Success_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['placeholder']['success'] = trlKwfStatic('Comment was successfully saved.');
        return $ret;
    }

    protected function _getTargetPage()
    {
        return $this->getData()->getParentPage();
    }
}
