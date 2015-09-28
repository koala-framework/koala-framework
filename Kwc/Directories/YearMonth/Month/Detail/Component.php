<?php
class Kwc_Directories_YearMonth_Month_Detail_Component extends Kwc_Directories_Month_Detail_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        return $ret;
    }

    public static function getItemDirectoryClasses($directoryClass)
    {
        return self::_getParentItemDirectoryClasses($directoryClass, 2);
    }

    protected function _getItemDirectory()
    {
        return $this->getData()->parent->parent->parent;
    }
}
