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
                        ->getComponentByClass(
                            'Vpc_User_Register_Component',
                            array('subroot' => $this->getData())
                        );
        return $ret;
    }

    public function processInput(array $postData)
    {
        // Leer, weil _processInput schon in proProcessInput aufgerufen wurde
    }

    public function preProcessInput($postData)
    {
        // TODO: Kopie von Vpc_User_BoxAbstract_Component wie anderes auf dieser Seite
        if (isset($postData['feAutologin'])
            && !Vps_Registry::get('userModel')->getAuthedUser()
        ) {
            list($cookieId, $cookieMd5) = explode('.', $postData['feAutologin']);
            if (!empty($cookieId) && !empty($cookieMd5)) {
                $this->_getAuthenticateResult($cookieId, $cookieMd5);
            }
        }
        $this->_processInput($postData);
        parent::preProcessInput($postData);
    }

    protected function _afterSave(Vps_Model_Row_Interface $row)
    {
        $result = $this->_getAuthenticateResult($row->email, $row->password);

        if ($result->isValid()) {
            $authedUser = Vps_Registry::get('userModel')->getAuthedUser();
            if ($row->auto_login) {
                $cookieValue = $authedUser->id.'.'.md5($authedUser->password);
                setcookie('feAutologin', $cookieValue, time() + (100*24*60*60));
            }
            $this->_afterLogin($authedUser);
        } else {
            $this->_errors[] = array('message' => trlVps('Invalid E-Mail or password, please try again.'));
        }
    }

    protected function _afterLogin(Vps_User_Row $user)
    {
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
