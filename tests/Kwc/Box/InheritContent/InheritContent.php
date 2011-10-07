<?php
class Vpc_Box_InheritContent_InheritContent extends Vpc_Box_InheritContent_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component'] = 'Vpc_Box_InheritContent_Content';
        return $ret;
    }

}
