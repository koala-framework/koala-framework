<?php
class Kwc_User_Activate_Form_Component extends Kwc_Form_Component
{
    const ERROR_DATA_NOT_COMPLETE = 'ednc';
    const ERROR_ALREADY_ACTIVATED = 'eaa';
    const ERROR_CODE_WRONG        = 'ecw';

    private $_user = null;
    private $_hideForm = false;

    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['placeholder']['submitButton'] = trlKwfStatic('Activate Account');
        $ret['generators']['child']['component']['success'] = 'Kwc_User_Activate_Form_Success_Component';
        $ret['useAjaxRequest'] = true;
        $ret['viewCache'] = false;
        unset($ret['plugins']['useViewCache']);
        return $ret;
    }

    protected function _getErrorMessage($type)
    {
        if ($type == self::ERROR_DATA_NOT_COMPLETE) {
            return trlKwf('Data was not sent completely. Please copy the complete address out of the email.');
        } else if ($type == self::ERROR_ALREADY_ACTIVATED) {
            return trlKwf('This account has already been activated.');
        } else if ($type == self::ERROR_CODE_WRONG) {
            return trlKwf('Activation code is wrong. Maybe the address was copied wrong out of the email?');
        }
        return null;
    }

    protected function _initForm()
    {
        parent::_initForm();
        $this->_form->setModel(new Kwf_Model_FnF());
        $this->_form->add(new Kwf_Form_Field_Hidden('code'));
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        if ($this->_hideForm) $ret['form'] = null;
        return $ret;
    }

    public function getUserRow()
    {
        return $this->_user;
    }

    public function processInput(array $postData)
    {
        parent::processInput($postData);

        if (isset($postData['code'])) {
            $code = $postData['code'];
            $this->getForm()->getRow()->code = $code;
        } else if (isset($postData['form_code'])) {
            $code = $postData['form_code'];
            $this->getForm()->getRow()->code = $code;
        } else {
            $code = $this->getForm()->getRow()->code;
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

        if ($this->_user && $this->isSaved()) {
            $userModel->setPassword($this->_user, $this->_form->getRow()->password);
            $this->_user->clearActivationToken();
            $this->_afterLogin(Kwf_Registry::get('userModel')->getAuthedUser());
        }
    }

    protected function _afterLogin(Kwf_Model_Row_Abstract $user)
    {
    }
}
