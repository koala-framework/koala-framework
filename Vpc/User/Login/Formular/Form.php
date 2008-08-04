<?php
class Vpc_User_Login_Formular_Form extends Vps_Form
{
    protected function _init()
    {
        parent::_init();
        $this->_model = new Vps_Model_FnF();

        $this->add(new Vps_Form_Field_TextField('email', trlVps('E-Mail')))
                    ->setAllowBlank(false)
                    ->setVType('email');

        $this->add(new Vps_Form_Field_Password('password', trlVps('Password')));

        //TODO: Validators wenn ungültiger login usw
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
        }
    }
}
