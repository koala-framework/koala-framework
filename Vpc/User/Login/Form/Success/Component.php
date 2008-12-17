<?php
class Vpc_User_Login_Form_Success_Component extends Vpc_Form_Success_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['viewCache'] = false;
        return $ret;
    }
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        if (is_instance_of($this->getData()->getPage()->componentClass, 'Vpc_User_Login_Component')) {
            $user = Vps_Registry::get('userModel')->getAuthedUser();
            $ret['redirectTo'] = Vps_Component_Data_Root::getInstance()
                ->getComponentByClass(
                    'Vpc_User_Directory_Component',
                    array('subroot' => $this->getData())
                )
                ->getChildComponent('_' . $user->id);
            $ret['redirectType'] = 'profile';
        } else {
            $ret['redirectTo'] = $this->getData()->getPage();
            $ret['redirectType'] = 'page';
        }
        return $ret;
    }
}
