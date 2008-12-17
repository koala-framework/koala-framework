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
        $this->_processInput($postData);
        parent::preProcessInput($postData);
    }

    protected function _afterSave(Vps_Model_Row_Interface $row)
    {
        $result = $this->_getAuthenticateResult($row->email, $row->password);

        if ($result->isValid()) {
            if ($row->auto_login) {
                $authedUser = Vps_Registry::get('userModel')->getAuthedUser();
                $cookieValue = $authedUser->id.'.'.md5($authedUser->password);
                setcookie('feAutologin', $cookieValue, time() + (100*24*60*60));
            }
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
