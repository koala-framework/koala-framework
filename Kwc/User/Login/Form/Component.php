<?php
class Kwc_User_Login_Form_Component extends Kwc_Form_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['placeholder']['submitButton'] = trlKwfStatic('Login');
        $ret['generators']['child']['component']['success'] = 'Kwc_User_Login_Form_Success_Component';
        $ret['plugins'][] = 'Kwc_User_Login_Form_UseViewCachePlugin';
        return $ret;
    }

    public function preProcessInput(array $postData)
    {
        $this->_processInput($postData);
        parent::preProcessInput($postData);
    }

    public function processInput(array $postData)
    {
        // Already called in preProcessInput
    }

    public function _getBaseParams()
    {
        $ret = parent::_getBaseParams();
        if (!empty($_GET['redirect'])) $ret['redirect'] = $_GET['redirect'];
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['register'] = $this->_getRegisterComponent();
        return $ret;
    }

    protected function _getRegisterComponent()
    {
        return Kwf_Component_Data_Root::getInstance()
            ->getComponentByClass(
                'Kwc_User_Register_Component',
                array('subroot' => $this->getData())
            );
    }

    protected function _afterSave(Kwf_Model_Row_Interface $row)
    {
        $result = $this->_getAuthenticateResult($row->email, $row->password);

        if ($result->isValid()) {
            $authedUser = Kwf_Registry::get('userModel')->getAuthedUser();
            if ($row->auto_login) {
                Kwf_User_Autologin::setCookies($authedUser);
            } else {
                //user logged in without autologin activated, clear the autologin token
                Kwf_User_Autologin::clearToken($authedUser);
            }
            $this->_afterLogin($authedUser);
        } else {
            if ($result->getMessages()) {
                foreach ($result->getMessages() as $message) {
                    $this->_errors[] = array('message' => $this->getData()->trlStaticExecute($message));
                }
            } else {
                $this->_errors[] = array('message' => $this->getData()->trlKwf('Invalid E-Mail or password, please try again.'));
            }
        }
    }

    protected function _afterLogin(Kwf_User_Row $user)
    {
    }

    private function _getAuthenticateResult($identity, $credential)
    {
        $adapter = new Kwf_Auth_Adapter_PasswordAuth();
        $adapter->setIdentity($identity);
        $adapter->setCredential($credential);

        $auth = Kwf_Auth::getInstance();
        $auth->clearIdentity();
        return $auth->authenticate($adapter);
    }
}
