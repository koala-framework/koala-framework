<?php
class Vpc_Formular_Dynamic_Component extends Vpc_Formular_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['childComponentClasses']['textfield'] = 'Vps_Form_Field_TextField';
        $ret['childComponentClasses']['select'] = 'Vps_Form_Field_Select';
        $ret['childComponentClasses']['text'] = 'Vpc_Basic_Text_Component';
        $ret['tablename'] = 'Vpc_Formular_Dynamic_Model';
        return $ret;
    }
}
