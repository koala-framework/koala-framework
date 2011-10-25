<?php
class Kwc_Box_InheritContent_InheritContent extends Kwc_Box_InheritContent_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component'] = 'Kwc_Box_InheritContent_Content';
        return $ret;
    }

}
