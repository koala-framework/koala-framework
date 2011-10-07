<?php
class Vpc_User_DeleteAccount_Form_Component extends Vpc_Form_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['placeholder']['submitButton'] = trlVps('Delete Account');
        $ret['plugins'] = array('Vps_Component_Plugin_Login_Component');
        return $ret;
    }

    protected function _initForm()
    {
        parent::_initForm();
        $this->_form->setModel(new Vps_Model_FnF());
    }

    protected function _afterSave(Vps_Model_Row_Interface $row)
    {
        parent::_afterSave($row);
        $user = Vps_Registry::get('userModel')->getAuthedUser();
        $user->deleted = 1;
        $user->save();

        Vps_Auth::getInstance()->clearIdentity();
        setcookie('feAutologin', '', time() - 3600);
    }
}
