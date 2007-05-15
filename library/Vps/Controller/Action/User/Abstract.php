<?php
class Vps_Controller_Action_User_Abstract extends Vps_Controller_Action
{
    protected function _showLoginForm($adapter, $action = '/user/login')
    {
        require_once 'HTML/QuickForm.php';

        $form = new HTML_QuickForm('firstForm', 'post', $action);
        $form->setDefaults(array(
            'name' => 'Joe User'
        ));
        $form->addElement('header', null, 'Login');
        $form->addElement('text', 'username', 'Benutzername:', array('size' => 50, 'maxlength' => 255));
        $form->addElement('password', 'password', 'Kennwort:', array('size' => 50, 'maxlength' => 255));
        $form->addElement('submit', null, 'Send');

        $form->applyFilter('username', 'trim');
        $form->applyFilter('password', 'password');

        $form->addRule('username', 'Bitte einen Benutzernamen eingeben.', 'required', null, 'client');
        $form->addRule('password', 'Bitte ein Kennwort eingeben.', 'required', null, 'client');
        $form->addRule('username', 'Bitte einen Benutzernamen eingeben.', 'required', null, 'server');
        $form->addRule('password', 'Bitte ein Kennwort eingeben.', 'required', null, 'server');

        if ($form->validate()) {
            $username = $form->exportValue('username');
            $password = $form->exportValue('password');
            
            $auth = Zend_Auth::getInstance();
            $adapter->setIdentity($username);
            $adapter->setCredential($password);
            $result = $auth->authenticate($adapter);
            
            
            if (!$result->isValid()) {
                foreach ($result->getMessages() as $message) {
                    echo "$message\n";
                }
            } else {
                echo "You are now logged in.";
                return $result;
                // TODO: redirect auf HP?
            }
            
        }

        $form->display();
        echo "Einloggen mit test / test";
    }

    protected function _logout()
    {
        Zend_Auth::getInstance()->clearIdentity();
        $adminSession = new Zend_Session_Namespace('admin');
        $adminSession->unsetAll();
        echo "You are now logged out.";
    }
}
