<?php
class Kwc_User_Login_Form_Component extends Kwc_Form_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['placeholder']['submitButton'] = trlKwfStatic('Login');
        $ret['generators']['child']['component']['success'] = 'Kwc_User_Login_Form_Success_Component';
        return $ret;
    }

    public function preProcessInput($postData)
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

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['register'] = Kwf_Component_Data_Root::getInstance()
                        ->getComponentByClass(
                            'Kwc_User_Register_Component',
                            array('subroot' => $this->getData())
                        );
        return $ret;
    }

    protected function _afterSave(Kwf_Model_Row_Interface $row)
    {
        $result = $this->_getAuthenticateResult($row->email, $row->password);

        if ($result->isValid()) {
            $authedUser = Kwf_Registry::get('userModel')->getKwfModel()->getAuthedUser();
            if ($row->auto_login) {
                $cookieValue = $authedUser->id.'.'.md5($authedUser->password);
                setcookie('feAutologin', $cookieValue, time() + (100*24*60*60), '/', null, Kwf_Util_Https::supportsHttps(), true);
                setcookie('hasFeAutologin', '1', time() + (100*24*60*60), '/', null, false, true);
            }
            $this->_afterLogin($authedUser);
        } else {
            $this->_errors[] = array('message' => $this->getData()->trlKwf('Invalid E-Mail or password, please try again.'));
        }
    }

    protected function _afterLogin(Kwf_User_Row $user)
    {
    }

    private function _getAuthenticateResult($identity, $credential)
    {
        $adapter = new Kwf_Auth_Adapter_Service();
        $adapter->setIdentity($identity);
        $adapter->setCredential($credential);

        $auth = Kwf_Auth::getInstance();
        $auth->clearIdentity();
        return $auth->authenticate($adapter);
    }
}
