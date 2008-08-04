<?php
class Vpc_User_Login_Formular_Component extends Vpc_Formular_Component
{
    protected $_formName = 'Vpc_User_Login_Formular_Form';

    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['placeholder']['submitButton'] = trlVps('Login');
        $ret['generators']['child']['component']['success'] = 'Vpc_User_Login_Formular_Success_Component';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['register'] = Vps_Component_Data_Root::getInstance()
                        ->getComponentByClass('Vpc_User_Register_Component');
        return $ret;
    }

}
