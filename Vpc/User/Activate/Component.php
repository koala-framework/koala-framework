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
                'name'=>'sbmt', 'width'=>200, 'text' => 'Konto aktivieren'
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
            return 'Daten wurden nicht komplett mitgeschickt. '
                .'Bitte kopieren Sie die komplette Adresse aus der Email.';
        }

        $users = Zend_Registry::get('userModel');
        $row = $users->find($userId)->current();

        if (!$row) {
            return 'User ID ist ungültig.';
        } else if ($row->getActivationCode() != $code) {
            return 'Aktivierungscode ist falsch. Eventuell ist Ihr Account bereits '
                .'aktiviert worden, oder die Adresse wurde falsch aus der Email kopiert.';
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
            throw new Vps_ClientException('Aktivierungs-Error: '.$error);
        }

        list($userId, $code) = explode('-', $this->_getParam('code'));
        $users = Zend_Registry::get('userModel');
        $row = $users->find($userId)->current();
        $status = $row->setPassword($password);

        Vpc_User_Login_Component::doLogin($row->email, $password);
    }
}
