<?php
class Vpc_User_Login_Component extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['form'] = 'Vpc_User_Login_Form_Component';
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
