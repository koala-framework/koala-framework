<?php
class Kwc_User_Login_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['form'] = 'Kwc_User_Login_Form_Component';
//         $ret['generators']['child']['component']['facebook'] = 'Kwc_User_Login_Facebook_Component';
        $ret['cssClass'] = 'webStandard';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['register'] = $this->_getRegisterComponent();
        $ret['lostPassword'] = $this->_getLostPasswordComponent();
        return $ret;
    }

    protected function _getRegisterComponent()
    {
        return Kwf_Component_Data_Root::getInstance()
            ->getComponentByClass(
                'Kwc_User_Register_Component',
                array('subroot' => $this->getData())
            );
    }

    protected function _getLostPasswordComponent()
    {
        return Kwf_Component_Data_Root::getInstance()
            ->getComponentByClass(
                'Kwc_User_LostPassword_Component',
                array('subroot' => $this->getData())
            );
    }
}
