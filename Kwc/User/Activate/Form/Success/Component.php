<?php
class Kwc_User_Activate_Form_Success_Component extends Kwc_Form_Success_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['assets']['files'][] = 'kwf/Kwc/User/Activate/Form/Success/Component.js';
        $ret['assets']['dep'][] = 'KwfOnReady';
        return $ret;
    }
}
