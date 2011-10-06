<?php
class Vpc_Directories_YearMonth_Month_Detail_Component extends Vpc_Directories_Month_Detail_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        return $ret;
    }
    protected function _getItemDirectory()
    {
        return $this->getData()->parent->parent->parent;
    }
}
