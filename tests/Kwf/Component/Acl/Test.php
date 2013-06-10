<?php
/**
 * @group Kwf_Component_Acl
 */
class Kwf_Component_Acl_Test extends Kwc_TestAbstract
{
    private $_acl;

    public function setUp()
    {
        parent::setUp('Kwf_Component_Acl_Root');
        $acl = new Kwf_Acl();
        $this->_acl = $acl->getComponentAcl();
        $acl->addRole(new Kwf_Acl_Role('test', 'MainRole'));
        $acl->addRole(new Kwf_Acl_Role_Additional('testsub', 'SubRole', 'test'));
    }

    public function testDefaultRule()
    {
        $this->assertFalse($this->_acl->isAllowed('test', 'Kwc_Basic_None_Component'));
        $this->assertFalse($this->_acl->isAllowed('guest', 'Kwc_Basic_None_Component'));
        $this->assertFalse($this->_acl->isAllowed(null, 'Kwc_Basic_None_Component'));
        $this->assertFalse($this->_acl->isAllowed('testsub', 'Kwc_Basic_None_Component'));
    }

    public function testAllowComponentAll()
    {
        $this->_acl->allowComponent('test', null);
        $this->assertFalse($this->_acl->isAllowed(null, 'Kwc_Basic_None_Component'));
        $this->assertTrue($this->_acl->isAllowed('test', 'Kwc_Basic_None_Component'));
        $this->assertTrue($this->_acl->isAllowed('test', 'Kwf_Component_Acl_Root'));
        $this->assertFalse($this->_acl->isAllowed('testsub', 'Kwc_Basic_None_Component'));
    }

    public function testAllowComponent()
    {
        $this->_acl->allowComponent('test', 'Kwf_Component_Acl_Empty2');
        $this->_acl->allowComponent('testsub', 'Kwf_Component_Acl_Empty2');
        $this->assertFalse($this->_acl->isAllowed(null, 'Kwc_Basic_None_Component'));
        $this->assertTrue($this->_acl->isAllowed('test', 'Kwf_Component_Acl_Empty2'));
        $this->assertFalse($this->_acl->isAllowed('test', 'Kwc_Basic_None_Component'));
        $this->assertFalse($this->_acl->isAllowed('test', 'Kwf_Component_Acl_Root'));
        $this->assertTrue($this->_acl->isAllowed('testsub', 'Kwf_Component_Acl_Empty2'));
        $this->assertFalse($this->_acl->isAllowed('testsub', 'Kwf_Component_Acl_Root'));
    }

    public function testAllowComponentChild()
    {
        $this->_acl->allowComponent('test', 'Kwf_Component_Acl_TestComponent');
        $this->assertTrue($this->_acl->isAllowed('test', 'Kwf_Component_Acl_TestComponent'));
        $this->assertTrue($this->_acl->isAllowed('test', 'Kwc_Basic_None_Component'));
    }

    public function testAllowComponentChildRoot()
    {
        $this->_acl->allowComponent('test', 'Kwf_Component_Acl_Root');
        $this->assertTrue($this->_acl->isAllowed('test', 'Kwf_Component_Acl_Root'));
        $this->assertTrue($this->_acl->isAllowed('test', 'Kwc_Basic_None_Component'));
        $this->assertTrue($this->_acl->isAllowed('test', 'Kwf_Component_Acl_Empty2'));
        $this->assertTrue($this->_acl->isAllowed('test', 'Kwf_Component_Acl_TestComponent'));
    }

    public function testHasAllowedChildComponents1()
    {
        $this->assertEquals(0, count($this->_acl->getAllowedRecursiveChildComponents('test', $this->_root)));
    }

    public function testHasAllowedChildComponents2()
    {
        $this->_acl->allowComponent('test', 'Kwf_Component_Acl_TestComponent');
        $this->assertEquals(1, count($this->_acl->getAllowedRecursiveChildComponents('test', $this->_root)));
    }

    public function testDynamicComponent1()
    {
        $this->assertFalse($this->_acl->isAllowed('test', $this->_root));
        $this->_acl->allowComponent('test', 'Kwf_Component_Acl_Root');
        $this->assertTrue($this->_acl->isAllowed('test', $this->_root));
        $this->assertTrue($this->_acl->isAllowed('test', $this->_root->getComponentById('root-title')));
        $this->assertFalse($this->_acl->isAllowed('test', $this->_root->getComponentById('1')));
        $this->assertFalse($this->_acl->isAllowed('test', $this->_root->getComponentById('3')));
        $this->assertFalse($this->_acl->isAllowed('test', $this->_root->getComponentById('4')));
        $this->assertFalse($this->_acl->isAllowed('test', $this->_root->getComponentById('4')));
    }

    public function testDynamicComponent2()
    {
        $this->_acl->allowComponent('test', 'Kwf_Component_Acl_TestComponent');
        $this->assertFalse($this->_acl->isAllowed('test', $this->_root));
        $this->assertFalse($this->_acl->isAllowed('test', $this->_root->getComponentById('1')));
        $this->assertTrue($this->_acl->isAllowed('test', $this->_root->getComponentById('3')));
        $this->assertFalse($this->_acl->isAllowed('test', $this->_root->getComponentById('4')));
    }

    public function testDynamicComponentDontAllowChildPage()
    {
        $this->_acl->allowComponent('test', 'Kwf_Component_Acl_TestComponent');
        $this->assertTrue($this->_acl->isAllowed('test', $this->_root->getComponentById('3')));
        $this->assertFalse($this->_acl->isAllowed('test', $this->_root->getComponentById('3_blub')));
    }

    public function testDynamicComponentDontAllowChildPseudoPage()
    {
        $this->_acl->allowComponent('test', 'Kwf_Component_Acl_TestComponent');
        $this->assertTrue($this->_acl->isAllowed('test', $this->_root->getComponentById('3')));
        $this->assertFalse($this->_acl->isAllowed('test', $this->_root->getComponentById('3-pseudoPage')));
    }

    public function testDynamicComponentAllowChildPage()
    {
        $this->_acl->allowComponentRecursive('test', 'Kwf_Component_Acl_TestComponent');
        $this->assertTrue($this->_acl->isAllowed('test', $this->_root->getComponentById('3')));
        $this->assertTrue($this->_acl->isAllowed('test', $this->_root->getComponentById('3_blub')));
    }

    public function testDynamicComponentAllowChildPseudoPage()
    {
        $this->_acl->allowComponentRecursive('test', 'Kwf_Component_Acl_TestComponent');
        $this->assertTrue($this->_acl->isAllowed('test', $this->_root->getComponentById('3')));
        $this->assertTrue($this->_acl->isAllowed('test', $this->_root->getComponentById('3-pseudoPage')));
    }

    public function testAdditionalUserRole()
    {
        $user = new Kwf_Component_Acl_User('test', array('testsub'));
        $this->_acl->allowComponent('testsub', 'Kwf_Component_Acl_TestComponent');
        $this->assertFalse($this->_acl->isAllowed('test', $this->_root->getComponentById('3')));
        $this->assertFalse($this->_acl->isAllowed('test', $this->_root->getComponentById('3-pseudoPage')));
        $this->assertTrue($this->_acl->isAllowed($user, $this->_root->getComponentById('3')));
        $this->assertFalse($this->_acl->isAllowed($user, $this->_root->getComponentById('3-pseudoPage')));

        $user = new Kwf_Component_Acl_User('test', array('foo'));
        $this->assertFalse($this->_acl->isAllowed($user, $this->_root->getComponentById('3')));

        $user = new Kwf_Component_Acl_User('guest', array('testsub'));
        $this->assertFalse($this->_acl->isAllowed($user, $this->_root->getComponentById('3')));
    }
}
