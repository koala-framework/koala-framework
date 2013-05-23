<?php
class Kwc_User_BoxWithoutLogin_LoggedIn_Component extends Kwc_Abstract
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
        $ret['authedUser'] = Kwf_Registry::get('userModel')->getAuthedUser();

        /*
        $userDir = Kwf_Component_Data_Root::getInstance()
            ->getComponentByClass(
                'Kwc_User_Directory_Component',
                array('subroot' => $this->getData())
            );
        if ($userDir) {
            //falls das doch mal benötigt wird, über ein setting ein/ausschaltbar machen
            $ret['myProfile'] = $userDir->getChildComponent('_' . $ret['authedUser']->id);
        }
        */

        $ret['links'] = $this->_getLinks();
        $ret['linkPostfix'] = $this->_getSetting('linkPostfix');

        $ret['logoutLink'] = '/kwf/user/logout';
        if (isset($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI']) {
            $ret['logoutLink'] .= '?redirect=' . $_SERVER['REQUEST_URI'];
        }
        return $ret;
    }

    protected function _getLinks()
    {
        $ret = Kwf_Component_Data_Root::getInstance()
            ->getComponentsByClass('Kwc_User_Edit_Component');
        return $ret;
    }
}
