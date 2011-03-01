<?php
/**
 * @group Vps_Component_Acl
 */
class Vps_Component_Acl_Test extends Vpc_TestAbstract
{
    private $_acl;

    public function setUp()
    {
        parent::setUp('Vps_Component_Acl_Root');
        $acl = new Vps_Acl();
        $this->_acl = $acl->getComponentAcl();
        $acl->addRole(new Zend_Acl_Role('test'));
    }

    public function testDefaultRule()
    {
        $this->assertFalse($this->_acl->isAllowed('test', 'Vpc_Basic_Empty_Component'));
        $this->assertFalse($this->_acl->isAllowed('guest', 'Vpc_Basic_Empty_Component'));
        $this->assertFalse($this->_acl->isAllowed(null, 'Vpc_Basic_Empty_Component'));
    }

    public function testAllowComponentAll()
    {
        $this->_acl->allowComponent('test', null);
        $this->assertFalse($this->_acl->isAllowed(null, 'Vpc_Basic_Empty_Component'));
        $this->assertTrue($this->_acl->isAllowed('test', 'Vpc_Basic_Empty_Component'));
        $this->assertTrue($this->_acl->isAllowed('test', 'Vps_Component_Acl_Root'));
    }

    public function testAllowComponent()
    {
        $this->_acl->allowComponent('test', 'Vps_Component_Acl_Empty2');
        $this->assertFalse($this->_acl->isAllowed(null, 'Vpc_Basic_Empty_Component'));
        $this->assertTrue($this->_acl->isAllowed('test', 'Vps_Component_Acl_Empty2'));
        $this->assertFalse($this->_acl->isAllowed('test', 'Vpc_Basic_Empty_Component'));
        $this->assertFalse($this->_acl->isAllowed('test', 'Vps_Component_Acl_Root'));
    }

    public function testAllowComponentChild()
    {
        $this->_acl->allowComponent('test', 'Vps_Component_Acl_TestComponent');
        $this->assertTrue($this->_acl->isAllowed('test', 'Vps_Component_Acl_TestComponent'));
        $this->assertTrue($this->_acl->isAllowed('test', 'Vpc_Basic_Empty_Component'));
    }

    public function testAllowComponentChildRoot()
    {
        $this->_acl->allowComponent('test', 'Vps_Component_Acl_Root');
        $this->assertTrue($this->_acl->isAllowed('test', 'Vps_Component_Acl_Root'));
        $this->assertTrue($this->_acl->isAllowed('test', 'Vpc_Basic_Empty_Component'));
        $this->assertTrue($this->_acl->isAllowed('test', 'Vps_Component_Acl_Empty2'));
        $this->assertTrue($this->_acl->isAllowed('test', 'Vps_Component_Acl_TestComponent'));
    }

    public function testAllowTag()
    {
        $this->_acl->allowComponent('test', null);
        $this->_acl->allowTag('test', 'test');
        $this->assertFalse($this->_acl->isAllowed(null, $this->_root->getComponentById(1)));
        $this->assertTrue($this->_acl->isAllowed('test', $this->_root->getComponentById(1)));
    }

    public function testAllowAllTag()
    {
        $this->_acl->allowComponent('test', null);
        $this->_acl->allowTag('test', null);
        $this->assertTrue($this->_acl->isAllowed('test', $this->_root->getComponentById(2)));
        $this->assertTrue($this->_acl->isAllowed('test', $this->_root->getComponentById(1)));
    }

    public function testAllowTagChildComponent()
    {
        $this->_acl->allowComponent('test', null);
        $this->_acl->allowTag('test', 'blub');
        $this->assertTrue($this->_acl->isAllowed('test', $this->_root->getComponentById(3)));
        $this->assertTrue($this->_acl->isAllowed('test', $this->_root->getComponentById('3-blub')));
    }

    public function testAllowTagChildPage()
    {
        $this->_acl->allowComponent('test', null);
        $this->_acl->allowTag('test', 'blub');
        $this->assertTrue($this->_acl->isAllowed('test', $this->_root->getComponentById(3)));
        $this->assertFalse($this->_acl->isAllowed('test', $this->_root->getComponentById(4)));
    }
}
