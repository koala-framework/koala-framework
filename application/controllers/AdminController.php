<?php
class AdminController extends Zend_Controller_Action
{
    public function indexAction()
    {
        echo "AdminController::indexAction()<br />";
    }

    public function norouteAction()
    {
        echo "AdminController::norouteAction()<br />";
    }

    public function loginAction()
    {
        require_once 'HTML/QuickForm.php';

        $form = new HTML_QuickForm('firstForm', 'post', '/admin/login');
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
            
            $dbConfig = new Zend_Config_Ini('../application/config.db.ini', 'web');
            $dbConfig = $dbConfig->database->asArray();
            $db = Zend_Db::factory('PDO_MYSQL', $dbConfig);

            $auth = Zend_Auth::getInstance();
            $adapter = new Zend_Auth_Adapter_DbTable($db, 'users', 'username', 'password', 'PASSWORD (?)');
            $adapter->setIdentity($username);
            $adapter->setCredential($password);
            $result = $auth->authenticate($adapter);
            
            if (!$result->isValid()) {
                foreach ($result->getMessages() as $message) {
                    echo "$message\n";
                }
            } else {
                echo "You are now logged in.";
            }
            
        }

        $form->display();
        echo "Einloggen mit test / test";
    }

    public function logoutAction()
    {
        Zend_Auth::getInstance()->clearIdentity();
        echo "You are now logged out.";
    }

    public function __call($methodName, $args)
    {
        echo "AdminController::__call()<br />";
    }
}
?>