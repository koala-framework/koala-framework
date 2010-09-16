<?php
class Vpc_User_Activate_Form_Component extends Vpc_Form_Component
{
    private $_user = null;
    private $_hideForm = false;

    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['placeholder']['submitButton'] = trlVps('Activate Account');
        $ret['generators']['child']['component']['success'] = 'Vpc_User_Activate_Form_Success_Component';
        return $ret;
    }

    protected function _initForm()
    {
        parent::_initForm();
        $this->_form->setModel(new Vps_Model_FnF());
        $this->_form->add(new Vps_Form_Field_Hidden('code'));
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        if ($this->_hideForm) $ret['form'] = null;
        return $ret;
    }

    public function processInput(array $postData)
    {
        parent::processInput($postData);

        if (isset($postData['code'])) {
            $code = $postData['code'];
            $this->_form->getRow()->code = $code;
        } else if (isset($postData['form_code'])) {
            $code = $postData['form_code'];
            $this->_form->getRow()->code = $code;
        } else {
            $code = $this->_form->getRow()->code;
        }
        $code = explode('-', $code);
        if (count($code) != 2 || empty($code[0]) || empty($code[1])) {
            $this->_errors[] = trlVps('Data was not sent completely. Please copy the complete address out of the email.');
            $this->_hideForm = true;
        } else {
            $userId = (int)$code[0];
            $code = $code[1];
            $userModel = Zend_Registry::get('userModel');
            $this->_user = $userModel->getRow($userModel->select()->whereEquals('id', $userId));
            if (!$this->_user) {
                $this->_errors[] = trlVps('Data was not sent completely. Please copy the complete address out of the email.');
                $this->_hideForm = true;
            } else if ($this->_user->getActivationCode() != $code && $this->_user->password) {
                $this->_errors[] = trlVps('This account has already been activated.');
                $this->_hideForm = true;
            } else if ($this->_user->getActivationCode() != $code && !$this->_user->password) {
                $this->_errors[] = trlVps('Activation code is wrong. Maybe the address was copied wrong out of the email?');
                $this->_hideForm = true;
            }
        }

        Vps_Auth::getInstance()->clearIdentity();

        if ($this->_user && $this->isSaved()) {
            $this->_user->setPassword($this->_form->getRow()->password);
            if (!$this->_user->logins) {
                $this->_user->logins = 0;
            }
            $this->_user->logins += 1;
            $this->_user->last_login = date('Y-m-d H:i:s');
            $this->_user->save();
            $auth = Vps_Auth::getInstance();
            $auth->getStorage()->write(array('userId' => $this->_user->id));
            $this->_afterLogin(Vps_Registry::get('userModel')->getAuthedUser());
        }
    }

    protected function _afterLogin(Vps_User_Row $user)
    {
    }
}
