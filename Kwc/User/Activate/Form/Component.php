<?php
class Kwc_User_Activate_Form_Component extends Kwc_Form_Component
{
    const ERROR_DATA_NOT_COMPLETE = 'ednc';
    const ERROR_ALREADY_ACTIVATED = 'eaa';
    const ERROR_CODE_WRONG        = 'ecw';

    private $_user = null;
    private $_hideForm = false;

    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['placeholder']['submitButton'] = trlKwfStatic('Activate Account');
        $ret['generators']['child']['component']['success'] = 'Kwc_User_Activate_Form_Success_Component';
        $ret['viewCache'] = false;
        $ret['flags']['processInput'] = true;
        return $ret;
    }

    protected function _getBaseParams()
    {
        $ret = parent::_getBaseParams();
        if (!empty($_GET['redirect'])) $ret['redirect'] = $_GET['redirect'];
        return $ret;
    }

    protected function _getErrorMessage($type)
    {
        if ($type == self::ERROR_DATA_NOT_COMPLETE) {
            return $this->getData()->trlKwf('Data was not sent completely. Please copy the complete address out of the email.');
        } else if ($type == self::ERROR_ALREADY_ACTIVATED) {
            return $this->getData()->trlKwf('This account has already been activated.');
        } else if ($type == self::ERROR_CODE_WRONG) {
            return $this->getData()->trlKwf('Activation code is wrong. Maybe the address was copied wrong out of the email?');
        }
        return null;
    }

    protected function _initForm()
    {
        parent::_initForm();
        $this->_form->setModel(new Kwf_Model_FnF());
        $this->_form->add(new Kwf_Form_Field_Hidden('code'));
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        if ($this->_hideForm) $ret['form'] = null;
        return $ret;
    }

    public function getUserRow()
    {
        return $this->_user;
    }

    public function processInput(array $postData)
    {
        if (isset($postData['code'])) {
            $code = $postData['code'];
            $this->getForm()->getRow()->code = $code;
        } else {
            $code = '';
        }

        if (!preg_match('#^(.*)-(\w*)$#', $code, $m)) {
            $this->_errors[] = array('message' => $this->_getErrorMessage(self::ERROR_DATA_NOT_COMPLETE));
            $this->_hideForm = true;
        } else {
            $userId = $m[1];
            $code = $m[2];
            $userModel = Zend_Registry::get('userModel');
            $this->_user = $userModel->getRow($userId);
            if (!$this->_user) {
                $this->_errors[] = array('message' => $this->_getErrorMessage(self::ERROR_DATA_NOT_COMPLETE));
                $this->_hideForm = true;
            } else if (!$this->_user->validateActivationToken($code) && $this->_user->isActivated()) {
                $this->_errors[] = array('message' => $this->_getErrorMessage(self::ERROR_ALREADY_ACTIVATED));
                $this->_hideForm = true;
            } else if (!$this->_user->validateActivationToken($code)) {
                $this->_errors[] = array('message' => $this->_getErrorMessage(self::ERROR_CODE_WRONG));
                $this->_hideForm = true;
            }
        }
    }

    protected function _afterLogin(Kwf_Model_Row_Abstract $user)
    {
    }

    protected function _afterSave(Kwf_Model_Row_Interface $row)
    {
        parent::_afterSave($row);
        $userModel = Zend_Registry::get('userModel');

        if (!preg_match('#^(.*)-(\w*)$#', $row->code, $m)) {
            throw new Kwf_Exception("Invalid code");
        }
        $userId = $m[1];
        $code = $m[2];
        $user = $userModel->getRow($userId);

        if (!$user) {
            throw new Kwf_Exception("Invalid code");
        } else if (!$user->validateActivationToken($code)) {
            throw new Kwf_Exception("Invalid code");
        }
        $userModel->setPassword($user, $row->password);
        $user->clearActivationToken();
        $this->_afterLogin(Kwf_Registry::get('userModel')->getAuthedUser());
    }
}
