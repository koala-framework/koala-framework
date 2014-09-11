<?php
class Kwf_User_AuthAutoLogin_Test extends Kwf_Test_TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->_m = Kwf_Model_Abstract::getInstance('Kwf_User_AuthAutoLogin_FnF');
    }

    public function testGetAuth()
    {
        $authMethods = $this->_m->getAuthMethods();
        $this->assertTrue(isset($authMethods['autoLogin']));
        $auth = $authMethods['autoLogin'];
        $this->assertTrue($auth instanceof Kwf_User_Auth_Interface_AutoLogin);
    }

    public function testGetRowById()
    {
        $authMethods = $this->_m->getAuthMethods();
        $auth = $authMethods['autoLogin'];
        $row = $auth->getRowById('1');
        $this->assertNotNull($row);
        $this->assertEquals(1, $row->id);

        $row = $auth->getRowById('999');
        $this->assertNull($row);
    }

    public function testGetRowByIdentityDeleted()
    {
        $authMethods = $this->_m->getAuthMethods();
        $auth = $authMethods['autoLogin'];
        $row = $auth->getRowById('2');
        $this->assertNull($row);
    }

    public function testGetRowByIdentityLocked()
    {
        $authMethods = $this->_m->getAuthMethods();
        $auth = $authMethods['autoLogin'];
        $row = $auth->getRowById('3');
        $this->assertNull($row);
    }

    public function testValidateAutoLoginToken()
    {
        $authMethods = $this->_m->getAuthMethods();
        $auth = $authMethods['autoLogin'];
        $row = $this->_m->getRow(1);
        $validToken = 'footokenxxx';
        $this->assertTrue($auth->validateAutoLoginToken($row, $validToken));
        $this->assertFalse($auth->validateAutoLoginToken($row, 'bar'));
    }

    public function testGenerateAutoLoginToken()
    {
        $authMethods = $this->_m->getAuthMethods();
        $auth = $authMethods['autoLogin'];
        $row = $this->_m->getRow(1);

        $oldAutologin = $row->autologin;
        $oldToken = 'footokenxxx';
        $this->assertTrue($auth->validateAutoLoginToken($row, $oldToken));

        $token = $auth->generateAutoLoginToken($row);
        $this->assertFalse($auth->validateAutoLoginToken($row, $oldToken));
        $this->assertTrue($auth->validateAutoLoginToken($row, $token));

        $this->assertTrue($oldAutologin != $row->autologin);
    }

    public function testClearAutoLoginToken()
    {
        $authMethods = $this->_m->getAuthMethods();
        $auth = $authMethods['autoLogin'];
        $row = $this->_m->getRow(1);
        $auth->clearAutoLoginToken($row);

        $this->assertNull($row->autologin);
    }

}
