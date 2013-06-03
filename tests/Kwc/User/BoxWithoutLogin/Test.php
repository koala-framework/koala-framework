<?php
class Kwc_User_BoxWithoutLogin_Test extends Kwc_TestAbstract
{
    private $_previousUserModel;
    public function setUp()
    {
        parent::setUp('Kwc_User_BoxWithoutLogin_Root');

        //use custom user model
        $this->_previousUserModel = Kwf_Registry::get('config')->user->model;
        Kwf_Registry::get('config')->user->model = 'Kwc_User_BoxWithoutLogin_UserModel';

        //unset existing userModel instance to get new one
        $reg = Kwf_Registry::getInstance()->set('userModel',
            Kwf_Model_Abstract::getInstance('Kwc_User_BoxWithoutLogin_UserModel')
        );
    }

    public function tearDown()
    {
        parent::tearDown();
        Kwf_Registry::get('config')->user->model = $this->_previousUserModel;
        Kwf_Registry::getInstance()->offsetUnset('userModel');
    }

    //tests if the in this test overwritten userModel works correctly
    public function testUserModel()
    {
        $u = Kwf_Registry::get('userModel')->getAuthedUser();
        $this->assertEquals($u->id, 1);
    }

    public function testLoggedIn()
    {
        $c = $this->_root->getComponentByClass('Kwc_User_BoxWithoutLogin_Box_Component');
        $html = str_replace("\n", '', $c->render());
        $this->assertRegExp("#.*bh@vivid-planet\.com.*<a href=\"/kwf/user/logout\">Logout</a>.*#", $html, 'Should contain the users data and a logout link');
    }

    public function testNotLoggedIn()
    {
        Kwf_Model_Abstract::getInstance('Kwc_User_BoxWithoutLogin_UserModel')->setAuthedUser(0);
        $c = $this->_root->getComponentByClass('Kwc_User_BoxWithoutLogin_Box_Component');
        $html = str_replace("\n", '', $c->render());
        $html = str_replace(' ', '', $html);
        $this->assertRegExp("#<divclass=\"kwcUserBoxWithoutLoginBox\"><ul></ul></div>#", $html, 'Isn\'t empty but should be because no user is logged in...');
    }
}
