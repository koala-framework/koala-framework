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
    
    protected function _afterSave(Vps_Model_Row_Interface $row)
    {
        $adapter = new Vps_Auth_Adapter_Service();
        $adapter->setIdentity($row->email);
        $adapter->setCredential($row->password);

        $auth = Vps_Auth::getInstance();
        $result = $auth->authenticate($adapter);

        if ($result->isValid()) {
            $loginData = array('userId' => $adapter->getUserId());
            $auth->getStorage()->write($loginData);
        } else {
            $this->_errors[] = trlVps('Invalid E-Mail or password, please try again.');
        }
    }
}
