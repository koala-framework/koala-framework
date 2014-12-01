<?php
class Kwc_User_ChangePassword_Form_Component extends Kwc_Form_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['placeholder']['submitButton'] = trlKwfStatic('Change Password');
        $ret['generators']['child']['component']['success'] = 'Kwc_User_ChangePassword_Form_Success_Component';
        $ret['plugins'] = array('Kwf_Component_Plugin_Login_Component');
        return $ret;
    }

    protected function _initForm()
    {
        parent::_initForm();
        $this->_form->setModel(new Kwf_Model_FnF());
    }

    protected function _afterSave(Kwf_Model_Row_Interface $row)
    {
        parent::_afterSave($row);
        $user = Kwf_Registry::get('userModel')->getAuthedUser();
        $user->setPassword($this->_form->getRow()->new_password);
        $user->save();
    }
}
