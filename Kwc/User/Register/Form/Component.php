<?php
class Vpc_User_Register_Form_Component extends Vpc_Form_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['placeholder']['submitButton'] = trlVps('create account');
        $ret['generators']['child']['component']['success'] = 'Vpc_User_Register_Form_Success_Component';
        $ret['standardRole'] = 'user';
        return $ret;
    }

    protected function _beforeSave(Vps_Model_Row_Interface $row)
    {
        parent::_beforeSave($row);
        $row->role = $this->_getSetting('standardRole');
    }

    protected function _initForm()
    {
        parent::_initForm();
        $this->_initUserForm();
    }

    protected function _initUserForm()
    {
        $detailClass = Vpc_Abstract::getChildComponentClass(
                    $this->getData()->parent->parent->componentClass, 'detail');
        $forms = Vpc_Abstract::getSetting($this->getData()->parent->componentClass, 'forms');
        $this->_form->addUserForms($detailClass, $forms);
    }
}
