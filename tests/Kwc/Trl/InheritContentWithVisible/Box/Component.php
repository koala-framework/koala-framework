<?php
class Vpc_Trl_InheritContentWithVisible_Box_Component extends Vpc_Box_InheritContent_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component'] = 'Vpc_Trl_InheritContentWithVisible_Box_Child_Component';
        return $ret;
    }
}
