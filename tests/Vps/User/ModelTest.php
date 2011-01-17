<?php
/**
 * @group User
 */
class Vps_User_ModelTest extends Vps_Test_TestCase
{
    private $_serviceFnf;
    private $_userModel;

    public function setUp()
    {
        parent::setUp();
        $this->_serviceFnf = new Vps_Model_FnF(array(
            'data' => array(
                array(
                    'id' => 5,
                    'email' => 'herbert@vivid-planet.com',
                    'deleted' => 0,
                    'password' => md5('7b'),
                    'password_salt' => 'b',
                    'gender' => 'male',
                    'title' => '',
                    'firstname' => 'Hans',
                    'lastname' => 'Huber',
                    'webcode' => 'asdfasdfasdfasdf',
                    'created' => date('Y-m-d H:i:s'),
                    'logins' => 0,
                    'last_login' => null,
                    'last_modified' => date('Y-m-d H:i:s'),
                    'locked' => 0
                ), array(
                    'id' => 7,
                    'email' => 'hans@vivid-planet.com',
                    'deleted' => 0,
                    'password' => md5('7b'),
                    'password_salt' => 'b',
                    'gender' => 'male',
                    'title' => '',
                    'firstname' => 'Hans',
                    'lastname' => 'Huber',
                    'webcode' => 'wctest',
                    'created' => date('Y-m-d H:i:s'),
                    'logins' => 0,
                    'last_login' => null,
                    'last_modified' => date('Y-m-d H:i:s'),
                    'locked' => 0
                )
            ),
            'columns' => array('id', 'email', 'deleted', 'password', 'password_salt',
                'gender', 'title', 'firstname', 'lastname', 'webcode',
                'created', 'logins', 'last_login', 'last_modified', 'locked')
        ));
        $this->_userModel = new Vps_User_UserModel(array(
            'proxyModel' => $this->_serviceFnf,
            'siblingModels' => array('webuser' => 'Vps_User_SiblingModel'),
            'mailClass' => 'Vps_User_MailClass',
            'log' => false
        ));
    }

    public function tearDown()
    {
        $this->assertFalse(Vps_User_Model::isLockedCreateUser());
    }

    public function testCreateAndUpdate()
    {
        // $this->_userModel erben und im init() gemockte row geben
        // zum checken des mail versands

        $r = $this->_userModel->createUserRow('foo@vivid-planet.com', 'wctest');
        $r->role = 'admin';
        $r->save();

        $this->assertEquals(8, $r->id);
        $this->assertEquals('admin', $r->role);
        $this->assertEquals(1, Vps_User_MailClass::$mailsSent);

        $r->webcode = '';
        $r->role = 'user';
        $r->save();

        $this->assertEquals('', $r->webcode);
        $this->assertEquals('user', $r->role);
    }

    public function testLostPassword()
    {
        $login = $this->_userModel->lostPassword('hans@vivid-planet.com');
        $this->assertEquals(1, Vps_User_MailClass::$mailsSent);
    }

    public function testLogin()
    {
        // wrong identd
        $login = $this->_userModel->login(md5('asdfkjh'.time()).'@vivid-planet.com', '7');
        $this->assertEquals(Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND, $login['zendAuthResultCode']);
        // wrong password
        $login = $this->_userModel->login('hans@vivid-planet.com', 'lakjshfd');
        $this->assertEquals(Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID, $login['zendAuthResultCode']);
        // wrong password & identd
        $login = $this->_userModel->login(md5('asdfkjh'.time()).'@vivid-planet.com', 'lakjshfd');
        $this->assertEquals(Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND, $login['zendAuthResultCode']);
        // test password
        $login = $this->_userModel->login('hans@vivid-planet.com', 'test');
        $this->assertEquals(Zend_Auth_Result::SUCCESS, $login['zendAuthResultCode']);
        // real password
        $login = $this->_userModel->login('hans@vivid-planet.com', '7');
        $this->assertEquals(Zend_Auth_Result::SUCCESS, $login['zendAuthResultCode']);

        // testing with cookie password
        // wrong identd
        $login = $this->_userModel->login(md5('asdfkjh'.time()).'@vivid-planet.com', md5(md5('7b')));
        $this->assertEquals(Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND, $login['zendAuthResultCode']);
        // wrong password
        $login = $this->_userModel->login('hans@vivid-planet.com', md5(md5('3rfcd')));
        $this->assertEquals(Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID, $login['zendAuthResultCode']);
        // wrong password & identd
        $login = $this->_userModel->login(md5('asdfkjh'.time()).'@vivid-planet.com', md5(md5('3rfcd')));
        $this->assertEquals(Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND, $login['zendAuthResultCode']);
        // real password
        $login = $this->_userModel->login('hans@vivid-planet.com', md5(md5('7b')));
        $this->assertEquals(Zend_Auth_Result::SUCCESS, $login['zendAuthResultCode']);
    }

    public function testSetPassword()
    {
        $login = $this->_userModel->login('hans@vivid-planet.com', '7');
        $this->assertEquals(Zend_Auth_Result::SUCCESS, $login['zendAuthResultCode']);

        $r = $this->_userModel->getRow(7);
        $r->setPassword('abc');
        $r->save();

        $login = $this->_userModel->login('hans@vivid-planet.com', '7');
        $this->assertEquals(Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID, $login['zendAuthResultCode']);

        $login = $this->_userModel->login('hans@vivid-planet.com', 'abc');
        $this->assertEquals(Zend_Auth_Result::SUCCESS, $login['zendAuthResultCode']);
    }

    public function testChangedMail()
    {
        $r = $this->_userModel->getRow(5);
        $r->email = 'newmail@vivid-planet.com';
        $r->save();
        $this->assertEquals(1, Vps_User_MailClass::$mailsSent);
    }


}
