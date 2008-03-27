<?php
class Vpc_User_Activate_Component extends Vpc_Formular_Component
{
    public static function getSettings()
    {
        $ret = array_merge(parent::getSettings(), array(
            'tablename'  => 'Vpc_Formular_Model',
            'hideInNews' => true
        ));
        $ret['childComponentClasses']['success'] = 'Vpc_User_Activate_Success_Component';
        return $ret;
    }

    protected function _init()
    {
        parent::_init();

        if (!$this->_checkUserdata()) {
//             d('ende');
            $c = $this->_createFieldComponent('DoublePassword', array());
            $c->store('fieldLabel', 'Passwort:');
            $c->store('isMandatory', true);
            $c->store('name', 'password');

            $c = $this->_createFieldComponent('Submit', array(
                'name'=>'sbmt', 'width'=>200, 'text' => trlVps('activate Account')
            ));
            $c->store('name', 'sbmt');
            $c->store('fieldLabel', '&nbsp;');
        }
    }

    private function _checkUserData()
    {
        if ($this->_getParam('code')) {
            list($userId, $code) = explode('-', $this->_getParam('code'));
        }

        if (empty($userId) || empty($code)) {
            return trlVps('Data was not sent completely. ')
                .trlVps('Please copy the complete address out of the email.');
        }

        $users = Zend_Registry::get('userModel');
        $row = $users->find($userId)->current();

        if (!$row) {
            return 'User ID ist ungültig.';
        } else if ($row->getActivationCode() != $code) {
            return trlVps('Activation code is wrong. Eventually your account has already been activated')
                .', or the address was copied wrong out of the email.';
        }

        return '';
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();

        $error = $this->_checkUserData();
        if ($error) $ret['errors'][] = $error;

        $ret['formTemplate'] = Vpc_Admin::getComponentFile('Vpc_Formular_Component', '', 'tpl');

        return $ret;
    }

    protected function _processForm($values)
    {
        $values = array();
        foreach ($this->getChildComponents() as $c) {
            if ($c instanceof Vpc_Formular_Field_Interface) {
                $name = $c->getStore('name');
                if ($name == 'password') {
                    $password = $c->getValue();
                }
            }
        }

        $error = $this->_checkUserdata();
        if (!empty($error)) {
            throw new Vps_ClientException(trlvps('Activation-Error: ').$error);
        }

        list($userId, $code) = explode('-', $this->_getParam('code'));
        $users = Zend_Registry::get('userModel');
        $row = $users->find($userId)->current();
        $status = $row->setPassword($password);

        Vpc_User_Login_Component::doLogin($row->email, $password);
    }
}
