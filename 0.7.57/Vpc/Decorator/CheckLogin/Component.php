<?php
class Vpc_Decorator_CheckLogin_Component extends Vpc_Decorator_Abstract
{

    public function getTemplateVars()
    {
        if (!Zend_Registry::get('userModel')->getAuthedUser()) {
            $login = Vpc_Abstract::createInstance($this->getDao(),
                    'Vpc_User_Login_Component', 0, 0, $this->getPageCollection());
            $ret = $login->getTemplateVars();
        } else {
            $ret = parent::getTemplateVars();
        }
        return $ret;
    }
}
