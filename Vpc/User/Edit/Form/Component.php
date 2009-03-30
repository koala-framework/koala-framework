<?php
class Vpc_User_Edit_Form_Component extends Vpc_Form_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['placeholder']['submitButton'] = trlVps('edit account');
        $ret['generators']['child']['component']['success'] = 'Vpc_User_Edit_Form_Success_Component';
        $ret['plugins'] = array('Vps_Component_Plugin_Login_Component');
        return $ret;
    }
    
    protected function _initForm()
    {
        parent::_initForm();
        $user = Vps_Registry::get('userModel')->getAuthedUser();
        if ($user) {
            $this->_form->setId($user->id);
        }

        $this->_initUserForm();
    }

    protected function _initUserForm()
    {
        $detailClass = Vpc_Abstract::getChildComponentClass($this->getData()->parent->parent->componentClass, 'detail');
        $forms = Vpc_Abstract::getSetting($this->getData()->parent->componentClass, 'forms');
        $this->_form->addUserForms($detailClass, $forms);
    }
}
