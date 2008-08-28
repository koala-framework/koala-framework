<?php
class Vpc_User_Activate_Form_Component extends Vpc_Form_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['placeholder']['submitButton'] = trlVps('Activate Account');
        $ret['generators']['child']['component']['success'] = 'Vpc_User_Activate_Form_Success_Component';
        return $ret;
    }
    
    private function _checkUserData()
    {
        if ($this->_getParam('code')) {
            list($userId, $code) = explode('-', $this->_getParam('code'));
        }

        if (empty($userId) || empty($code)) {
            return trlVps('Data was not sent completely. Please copy the complete address out of the email.');
        }

        $users = Zend_Registry::get('userModel');
        $row = $users->find($userId)->current();

        if (!$row) {
            return 'User ID ist ungültig.';
        } else if ($row->getActivationCode() != $code) {
            return trlVps('Activation code is wrong. Eventually your account has already been activated, or the address was copied wrong out of the email.');
        }

        return '';
    }

    protected function _processForm($values)
    {
        $values = array();
        foreach ($this->getChildComponents() as $c) {
            if ($c instanceof Vpc_Form_Field_Interface) {
                $name = $c->getStore('name');
                if ($name == 'password') {
                    $password = $c->getValue();
                }
            }
        }

        $error = $this->_checkUserdata();
        if (!empty($error)) {
            throw new Vps_ClientException(trlvps('Activation-Error').': '.$error);
        }

        list($userId, $code) = explode('-', $this->_getParam('code'));
        $users = Zend_Registry::get('userModel');
        $row = $users->find($userId)->current();
        $status = $row->setPassword($password);

        $auth = Vps_Auth::getInstance();
        $auth->getStorage()->write(array('userId' => $row->id()));
    }
}
