<?php
class Vpc_User_BoxWithoutLogin_Component extends Vpc_User_BoxAbstract_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['showLostPassword'] = true;
        $ret['linkPostfix'] = '';
        return $ret;
    }
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['register'] = Vps_Component_Data_Root::getInstance()
                        ->getComponentByClass(
                            'Vpc_User_Register_Component',
                            array('subroot' => $this->getData())
                        );
        if ($this->_getSetting('showLostPassword')) {
            $ret['lostPassword'] = Vps_Component_Data_Root::getInstance()
                            ->getComponentByClass(
                                'Vpc_User_LostPassword_Component',
                                array('subroot' => $this->getData())
                            );
        }
        $ret['login'] = Vps_Component_Data_Root::getInstance()
                        ->getComponentByClass(
                            'Vpc_User_Login_Component',
                            array('subroot' => $this->getData())
                        );
        if ($ret['authedUser']) {
            $ret['myProfile'] = Vps_Component_Data_Root::getInstance()
                ->getComponentByClass(
                    'Vpc_User_Directory_Component',
                    array('subroot' => $this->getData())
                )
                ->getChildComponent('_' . $ret['authedUser']->id);
            $ret['links'] = $this->_getLinks();
        }

        $ret['linkPostfix'] = $this->_getSetting('linkPostfix');
        return $ret;
    }

    protected function _getLinks()
    {
        $ret = Vps_Component_Data_Root::getInstance()
            ->getComponentsByClass('Vpc_User_Edit_Component');
        return $ret;
    }

}
