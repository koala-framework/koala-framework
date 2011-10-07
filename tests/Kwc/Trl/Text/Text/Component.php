<?php
class Vpc_Trl_Text_Text_Component extends Vpc_Basic_Text_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Vpc_Trl_Text_Text_TestModel';
        $ret['generators']['child']['model'] = 'Vpc_Trl_Text_Text_TestChildComponentsModel';
        return $ret;
    }
}
