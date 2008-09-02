<?php
class Vpc_User_Box_Component extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['login'] = 'Vpc_User_Box_Login_Component';
        $ret['viewCache'] = false;
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['authedUser'] = Vps_Registry::get('userModel')->getAuthedUser();
        $ret['register'] = Vps_Component_Data_Root::getInstance()
                        ->getComponentByClass('Vpc_User_Register_Component');
        $ret['lostPassword'] = Vps_Component_Data_Root::getInstance()
                        ->getComponentByClass('Vpc_User_LostPassword_Component');
        if ($ret['authedUser']) {
            $ret['myProfile'] = Vps_Component_Data_Root::getInstance()
                ->getComponentByClass('Vpc_User_Directory_Component')
                ->getChildComponent('_' . $ret['authedUser']->id);
            $ret['links'] = $this->_getLinks();
        }
        return $ret;
    }
    
    protected function _getLinks()
    {
        return Vps_Component_Data_Root::getInstance()
            ->getComponentsByClass('Vpc_User_Edit_Component');
    }
}
