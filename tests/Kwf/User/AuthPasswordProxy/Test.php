<?php
class Kwf_User_AuthPasswordProxy_Test extends Kwf_Test_TestCase
{

    public function setUp()
    {
        parent::setUp();
        $this->_m = Kwf_Model_Abstract::getInstance('Kwf_User_AuthPasswordProxy_UserModel');
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
        $this->assertInstanceOf('Kwf_User_Row', $row);
        $this->assertEquals(1, $row->id);

        $row = $auth->getRowByIdentity('non@existent.com');
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

    public function testSetPassword()
    {
        $authMethods = $this->_m->getAuthMethods();
        $auth = $authMethods['password'];
        $row = $this->_m->getRow(1);
        $auth->setPassword($row, 'blubb');
        $this->assertFalse($auth->validatePassword($row, 'foo'));
        $this->assertTrue($auth->validatePassword($row, 'blubb'));
    }
}
