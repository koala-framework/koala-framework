<?php
class Kwc_User_Register_Form_Component extends Kwc_Form_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['placeholder']['submitButton'] = trlKwf('create account');
        $ret['generators']['child']['component']['success'] = 'Kwc_User_Register_Form_Success_Component';
        $ret['standardRole'] = 'user';
        return $ret;
    }

    protected function _beforeSave(Kwf_Model_Row_Interface $row)
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
        $detailClass = Kwc_Abstract::getChildComponentClass(
                    $this->getData()->parent->parent->componentClass, 'detail');
        $forms = Kwc_Abstract::getSetting($this->getData()->parent->componentClass, 'forms');
        $this->_form->addUserForms($detailClass, $forms);
    }
}
