<?php
class Vpc_Trl_Text_Text_Trl_Component extends Vpc_Basic_Text_Trl_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['text']['component'] = 'Vpc_Trl_Text_Text_Component';
        return $ret;
    }
}
