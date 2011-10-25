<?php
class Kwc_Trl_Text_Text_Component extends Kwc_Basic_Text_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Kwc_Trl_Text_Text_TestModel';
        $ret['generators']['child']['model'] = 'Kwc_Trl_Text_Text_TestChildComponentsModel';
        return $ret;
    }
}
