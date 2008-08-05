<?php
class Vpc_User_Box_Component extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['login'] = 'Vpc_User_Login_Component';
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
            $ret['links'] = $this->_getLinks();
        }
        return $ret;
    }
    protected function _getLinks()
    {
        $ret = array();

        //Mein Profil
        $ret[] = Vps_Component_Data_Root::getInstance()
            ->getComponentByClass('Vpc_User_Directory_Component')
            ->getChildComponent('_'.Vps_Registry::get('userModel')->getAuthedUser()->id);

        //Einstellungen
        $ret = array_merge($ret, Vps_Component_Data_Root::getInstance()
                        ->getComponentsByClass('Vpc_User_Edit_Component'));

        return $ret;
    }
}
