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
        $ret['redirectTo'] = $this->_getRedirectToPage();
        return $ret;
    }

    protected function _getRedirectToPage()
    {
        if (is_instance_of($this->getData()->getPage()->componentClass, 'Vpc_User_Login_Component')) {
            $user = Vps_Registry::get('userModel')->getAuthedUser();
            $userDir = Vps_Component_Data_Root::getInstance()
                ->getComponentByClass(
                    'Vpc_User_Directory_Component',
                    array('subroot' => $this->getData())
                );
            if ($userDir) {
                return $userDir->getChildComponent('_' . $user->id);
            } else {
                return Vps_Component_Data_Root::getInstance()
                    ->getChildPage(array('home' => true, 'subroot'=>$this->getData()));
            }
        } else {
            return $this->getData()->getPage();
        }
    }
}
