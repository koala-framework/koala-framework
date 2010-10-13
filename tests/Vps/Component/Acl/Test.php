<?php
/**
 * @group Vps_Component_Acl
 */
class Vps_Component_Acl_Test extends PHPUnit_Framework_TestCase
{
    private $_acl;
    private $_root;

    public function setUp()
    {
        Vps_Component_Data_Root::setComponentClass('Vps_Component_Acl_Root');
        $this->_root = Vps_Component_Data_Root::getInstance();

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

    public function testHasAllowedChildComponents1()
    {
        $this->assertEquals(0, count($this->_acl->getAllowedRecursiveChildComponents('test', $this->_root)));
    }

    public function testHasAllowedChildComponents2()
    {
        $this->_acl->allowComponent('test', 'Vps_Component_Acl_TestComponent');
        $this->assertEquals(1, count($this->_acl->getAllowedRecursiveChildComponents('test', $this->_root)));
    }

    public function testDynamicComponent1()
    {
        $this->assertFalse($this->_acl->isAllowed('test', $this->_root));
        $this->_acl->allowComponent('test', 'Vps_Component_Acl_Root');
        $this->assertTrue($this->_acl->isAllowed('test', $this->_root));
        $this->assertTrue($this->_acl->isAllowed('test', $this->_root->getComponentById('root-title')));
        $this->assertFalse($this->_acl->isAllowed('test', $this->_root->getComponentById('1')));
        $this->assertFalse($this->_acl->isAllowed('test', $this->_root->getComponentById('3')));
        $this->assertFalse($this->_acl->isAllowed('test', $this->_root->getComponentById('4')));
        $this->assertFalse($this->_acl->isAllowed('test', $this->_root->getComponentById('4')));
    }

    public function testDynamicComponent2()
    {
        $this->_acl->allowComponent('test', 'Vps_Component_Acl_TestComponent');
        $this->assertFalse($this->_acl->isAllowed('test', $this->_root));
        $this->assertFalse($this->_acl->isAllowed('test', $this->_root->getComponentById('1')));
        $this->assertTrue($this->_acl->isAllowed('test', $this->_root->getComponentById('3')));
        $this->assertFalse($this->_acl->isAllowed('test', $this->_root->getComponentById('4')));
    }
}
