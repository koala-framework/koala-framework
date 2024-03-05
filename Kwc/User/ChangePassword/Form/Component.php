<?php
class Kwc_User_ChangePassword_Form_Component extends Kwc_Form_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['placeholder']['submitButton'] = trlKwfStatic('Change Password');
        $ret['generators']['child']['component']['success'] = 'Kwc_User_ChangePassword_Form_Success_Component';
        $ret['plugins'] = array('Kwf_Component_Plugin_Login_Component');
        $ret['viewCache'] = false;
        $ret['flags']['processInput'] = true;
        return $ret;
    }

    protected function _initForm()
    {
        parent::_initForm();
        $this->_form->setModel(new Kwf_Model_FnF());
    }

    public function processInput($postData)
    {
        $users = Kwf_Registry::get('userModel');
        $userRow = $users->getAuthedUser();
        if (!$userRow) return;

        $showPassword = false;
        //is there a password auth?
        foreach ($users->getAuthMethods() as $auth) {
            if ($auth instanceof Kwf_User_Auth_Interface_Password) {
                $showPassword = true;
            }
        }
        if (!$showPassword) throw new Kwf_Exception("No password auth method found");

        //if a redirect auth doesn't allow password hide it
        foreach ($users->getAuthMethods() as $auth) {
            if ($auth instanceof Kwf_User_Auth_Interface_Redirect) {
                if (!$auth->allowPasswordForUser($userRow)) {
                    $label = $auth->getLoginRedirectLabel();
                    $label = Kwf_Trl::getInstance()->trlStaticExecute($label['name']);
                    $msg = $this->getData()->trlKwf("This user doesn't have a password, he must log in using {0}", $label);
                    $this->_errors[] = array(
                        'messages' => array($msg)
                    );
                    break;
                }
            }
        }
    }

    protected function _afterSave(Kwf_Model_Row_Interface $row)
    {
        parent::_afterSave($row);
        $user = Kwf_Registry::get('userModel')->getAuthedUser();
        $user->setPassword($this->_form->getRow()->new_password);
        $user->clearActivationToken();
    }
}
