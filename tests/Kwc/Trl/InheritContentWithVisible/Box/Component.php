<?php
class Kwc_Trl_InheritContentWithVisible_Box_Component extends Kwc_Box_InheritContent_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component'] = 'Kwc_Trl_InheritContentWithVisible_Box_Child_Component';
        return $ret;
    }
}
