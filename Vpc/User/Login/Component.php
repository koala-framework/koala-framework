<?php
class Vpc_User_Login_Component extends Vpc_Formular_Component
{
    protected function _init()
    {
        parent::_init();

        $fieldSettings = array('name'  => 'email',
                               'width' => 200);
        $c = $this->_createFieldComponent('Textbox', $fieldSettings);
        $c->store('name', 'email');
        $c->store('fieldLabel', 'Email');
        $c->store('isMandatory', true);

        $fieldSettings = array('name'  => 'password',
                               'width' => 200);
        $c = $this->_createFieldComponent('Password', $fieldSettings);
        $c->store('name', 'password');
        $c->store('fieldLabel', 'Passwort');
        $c->store('isMandatory', true);

        $c = $this->_createFieldComponent('Submit', array(
            'name'=>'sbmt', 'width'=>200, 'text' => 'Login'
        ));
        $c->store('name', 'sbmt');
        $c->store('fieldLabel', '&nbsp;');
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['formTemplate'] = Vpc_Admin::getComponentFile(get_parent_class($this), 'Component', 'tpl');
        $ret['redirectTo'] = $_SERVER['REQUEST_URI'];
        $ret['redirectTo'] = preg_replace('/(\?)logout=?[^&]*&?/', '$1', $ret['redirectTo']);
        $ret['loggedIn'] = false;
        if (Zend_Registry::get('userModel')->getAuthedUser()) {
            $ret['loggedIn'] = true;
        }

        if ($ret['sent'] == 3 && !$ret['loggedIn']) {
            $ret['sent'] = 2;
            $ret['errors'][] = 'Email oder Passwort ist falsch. Bitte versuchen Sie es erneut..';
        }

        $ret['registerUrl'] = '';
        $registerComponent = $this->getParentComponent();
        if ($registerComponent) {
            $ret['registerUrl'] = $registerComponent->getUrl();
        }
        return $ret;
    }

    static public function doLogin($email, $password)
    {
        $adapter = new Vps_Auth_Adapter_Service();
        $adapter->setIdentity($email);
        $adapter->setCredential($password);

        $auth = Vps_Auth::getInstance();
        $result = $auth->authenticate($adapter);

        if ($result->isValid()) {
            $loginData = array('userId' => $adapter->getUserId());
            $auth->getStorage()->write($loginData);
        }
    }

    protected function _processForm()
    {
        $values = array();
        foreach ($this->getChildComponents() as $c) {
            if ($c instanceof Vpc_Formular_Field_Interface) {
                $name = $c->getStore('name');
                if ($name == 'email' || $name == 'password') {
                    $values[$name] = $c->getValue();
                }
            }
        }

        self::doLogin($values['email'], $values['password']);
    }
}
