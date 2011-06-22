<?php
class Vpc_User_BoxWithoutLogin_LoggedIn_Component extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['viewCache'] = false;
        $ret['linkPostfix'] = '';
        $ret['cssClass'] = 'webStandard';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['authedUser'] = Vps_Registry::get('userModel')->getAuthedUser();
        $userDir = Vps_Component_Data_Root::getInstance()
            ->getComponentByClass(
                'Vpc_User_Directory_Component',
                array('subroot' => $this->getData())
            );
        if ($userDir) {
            $ret['myProfile'] = $userDir->getChildComponent('_' . $ret['authedUser']->id);
        }
        $ret['links'] = $this->_getLinks();
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
