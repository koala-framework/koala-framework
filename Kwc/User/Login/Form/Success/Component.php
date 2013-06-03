<?php
class Kwc_User_Login_Form_Success_Component extends Kwc_Form_Success_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['viewCache'] = false;
        $ret['assets']['files'][] = 'kwf/Kwc/User/Login/Form/Success/Component.js';
        $ret['assets']['dep'][] = 'KwfOnReady';
        return $ret;
    }
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['redirectTo'] = $this->_getRedirectToPage();
        if (!is_string($ret['redirectTo'])) {
            $ret['redirectToUrl'] = $ret['redirectTo']->url;
        } else {
            $ret['redirectToUrl'] =$ret['redirectTo'];
        }
        return $ret;
    }

    protected function _getRedirectToPage()
    {
        if (!empty($_REQUEST['redirect']) && substr($_REQUEST['redirect'], 0, 1) == '/') {
            return $_REQUEST['redirect'];
        }
        if (is_instance_of($this->getData()->getPage()->componentClass, 'Kwc_User_Login_Component') || is_instance_of($this->getData()->getPage()->componentClass, 'Kwc_User_Login_Trl_Component')) {
            $user = Kwf_Registry::get('userModel')->getAuthedUser();
            $userDir = Kwf_Component_Data_Root::getInstance()
                ->getComponentByClass(
                    'Kwc_User_Directory_Component',
                    array('subroot' => $this->getData())
                );
            if ($userDir) {
                return $userDir->getChildComponent('_' . $user->id);
            } else {
                return Kwf_Component_Data_Root::getInstance()
                    ->getChildPage(array('home' => true, 'subroot'=>$this->getData()));
            }
        } else {
            return $this->getData()->getPage();
        }
    }
}
