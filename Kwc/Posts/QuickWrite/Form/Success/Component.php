<?php
class Kwc_Posts_QuickWrite_Form_Success_Component extends Kwc_Posts_Write_Form_Success_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        return $ret;
    }

    protected function _getTargetPage()
    {
        return $this->getData()->getPage();
    }
}
