<?php
class Vpc_User_Login_Form_Component extends Vpc_Form_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['placeholder']['submitButton'] = trlVps('Login');
        $ret['generators']['child']['component']['success'] = 'Vpc_User_Login_Form_Success_Component';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['register'] = Vps_Component_Data_Root::getInstance()
                        ->getComponentByClass('Vpc_User_Register_Component');
        return $ret;
    }

    public function processInput($postData)
    {
        if (isset($postData['feAutologin'])
            && !Vps_Registry::get('userModel')->getAuthedUser()
        ) {
            list($cookieId, $cookieMd5) = explode('.', $postData['feAutologin']);
            if (!empty($cookieId) && !empty($cookieMd5)) {
                $result = $this->_getAuthenticateResult($cookieId, $cookieMd5);
            }
        }

        if (isset($postData['logout'])) {
            Vps_Auth::getInstance()->clearIdentity();
            setcookie('feAutologin', '', time() - 3600);
        }
        parent::processInput($postData);
    }

    protected function _afterSave(Vps_Model_Row_Interface $row)
    {
        $result = $this->_getAuthenticateResult($row->email, $row->password);

        if ($result->isValid() && $row->auto_login) {
            $authedUser = Vps_Registry::get('userModel')->getAuthedUser();
            $cookieValue = $authedUser->id.'.'.md5($authedUser->password);
            setcookie('feAutologin', $cookieValue, time() + (100*24*60*60));
        } else {
            $this->_errors[] = trlVps('Invalid E-Mail or password, please try again.');
        }
    }

    private function _getAuthenticateResult($identity, $credential)
    {
        $adapter = new Vps_Auth_Adapter_Service();
        $adapter->setIdentity($identity);
        $adapter->setCredential($credential);

        $auth = Vps_Auth::getInstance();
        $auth->clearIdentity();
        $result = $auth->authenticate($adapter);

        if ($result->isValid()) {
            $auth->getStorage()->write(array(
                'userId' => $adapter->getUserId()
            ));
        }

        return $result;
    }
}
