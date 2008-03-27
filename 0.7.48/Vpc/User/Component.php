<?php
class Vpc_User_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = 'User Registration';
        if (!isset($ret['childComponentClasses'])) $ret['childComponentClasses'] = array();
        $ret['childComponentClasses'] = array_merge($ret['childComponentClasses'], array(
            'activate' => 'Vpc_User_Activate_Component',
            'edit'     => 'Vpc_User_Edit_Component',
            'login'    => 'Vpc_User_Login_Component',
            'register' => 'Vpc_User_Register_Component'
        ));
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $children = $this->_getSetting('childComponentClasses');
        $ret['register'] = $this->createComponent($children['register'], 'register')
                                ->getTemplateVars();
        return $ret;
    }
}