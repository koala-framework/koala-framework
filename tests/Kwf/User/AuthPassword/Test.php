<?php
class Kwf_User_AuthPassword_Test extends Kwf_Test_TestCase
{

    public function setUp()
    {
        parent::setUp();
        $this->_m = Kwf_Model_Abstract::getInstance('Kwf_User_AuthPassword_FnF');
    }

    public function testGetAuth()
    {
        $authMethods = $this->_m->getAuthMethods();
        $this->assertTrue(isset($authMethods['password']));
        $auth = $authMethods['password'];
        $this->assertTrue($auth instanceof Kwf_User_Auth_Interface_Password);
    }

    public function testGetRowByIdentity()
    {
        $authMethods = $this->_m->getAuthMethods();
        $auth = $authMethods['password'];
        $row = $auth->getRowByIdentity('test@vivid.com');
        $this->assertNotNull($row);
        $this->assertEquals(1, $row->id);

        $row = $auth->getRowByIdentity('non@existent.com');
        $this->assertNull($row);
    }

    public function testGetRowByIdentityDeleted()
    {
        $authMethods = $this->_m->getAuthMethods();
        $auth = $authMethods['password'];
        $row = $auth->getRowByIdentity('testdel@vivid.com');
        $this->assertNull($row);
    }

    public function testValidatePassword()
    {
        $authMethods = $this->_m->getAuthMethods();
        $auth = $authMethods['password'];
        $row = $this->_m->getRow(1);
        $this->assertTrue($auth->validatePassword($row, 'foo'));
        $this->assertFalse($auth->validatePassword($row, 'bar'));
    }

    public function testSetPasswordMd5()
    {
        $authMethods = $this->_m->getAuthMethods();
        $auth = $authMethods['password'];
        $row = $this->_m->getRow(1);

        $oldPasswod = $row->password;
        $oldSalt = $row->password_salt;
        $auth->setPasswordHashMethod('md5');
        $auth->setPassword($row, 'blubb');
        $this->assertTrue(!preg_match('#^\$2a\$#', $row->password));
        $this->assertFalse($auth->validatePassword($row, 'foo'));
        $this->assertTrue($auth->validatePassword($row, 'blubb'));

        $this->assertTrue($oldPasswod != $row->password);
        $this->assertTrue($oldSalt != $row->password_salt);
    }
    
    public function testSetPasswordBcrypt()
    {
        $authMethods = $this->_m->getAuthMethods();
        $auth = $authMethods['password'];
        $row = $this->_m->getRow(1);
        $oldPasswod = $row->password;
        $oldSalt = $row->password_salt;
        $auth->setPassword($row, 'blubb');
        $this->assertTrue(!!preg_match('#^\$2a\$#', $row->password));
        $this->assertFalse($auth->validatePassword($row, 'foo'));
        $this->assertTrue($auth->validatePassword($row, 'blubb'));

        $this->assertTrue($oldPasswod != $row->password);
        //the bcrypt doesn't touch the password_salt
        $this->assertTrue($oldSalt == $row->password_salt);
    }

    public function testSendLostPasswordMail()
    {
        $authMethods = $this->_m->getAuthMethods();
        $auth = $authMethods['password'];
        $row = $this->_m->getRow(1);

        $transport = new Kwf_Mail_Transport_Test();
        $auth->setMailTransport($transport);

        $kwfUserRow = Kwf_Model_Abstract::getInstance('Kwf_User_AuthPassword_UserModel')->getRow(1);
        $auth->sendLostPasswordMail($row, $kwfUserRow);

        $this->assertEquals('test@vivid.com', $transport->recipients);
        $this->assertContains('lost-password', $transport->body);
    }
}
