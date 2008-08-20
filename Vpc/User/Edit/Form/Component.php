<?php
class Vpc_User_Edit_Form_Component extends Vpc_Form_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['placeholder']['submitButton'] = trlVps('edit account');
        $ret['generators']['child']['component']['success'] = 'Vpc_User_Edit_Form_Success_Component';
        $ret['cssClass'] = 'webStandard';
        return $ret;
    }
    
    protected function _initForm()
    {
        parent::_initForm();
        $user = Vps_Registry::get('userModel')->getAuthedUser();
        if (!$user) {
            //TODO:
            header('Location: /');
            exit;
        }
        $this->_form->setId($user->id);

        $detailClass = Vpc_Abstract::getChildComponentClass($this->getData()->parent->parent->componentClass, 'detail');
        $this->_form->setUserDetailsComponent($detailClass);

        $this->_form->setUserEditForms(Vpc_Abstract::getSetting($this->getData()->parent->componentClass, 'forms'));
    }
}
