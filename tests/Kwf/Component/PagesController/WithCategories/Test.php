<?php
/**
 * @group PagesController
 * @group Kwf_Component_Acl
 */
class Kwf_Component_PagesController_WithCategories_Test extends Kwc_TestAbstract
{
    private $_acl;
    public function setUp()
    {
        parent::setUp('Kwf_Component_PagesController_WithCategories_Root');
        $acl = new Kwf_Acl();
        $this->_acl = $acl->getComponentAcl();
        $acl->addRole(new Zend_Acl_Role('test'));
        $this->_acl->allowComponent('test', null);
    }

    public function testNodeConfig()
    {
        $user = 'test';
        $c = Kwf_Component_Data_Root::getInstance();
        $cfg = Kwf_Controller_Action_Component_PagesController::getComponentNodeConfig($c, $user, $this->_acl);
        $this->assertFalse($cfg['actions']['add']);
        $this->assertFalse($cfg['allowDrop']);
        $this->assertFalse($cfg['actions']['delete']);
        $this->assertFalse($cfg['actions']['makeHome']);
        $this->assertFalse($cfg['allowDrag']);

        $c = Kwf_Component_Data_Root::getInstance()->getComponentById('root-main');
        $cfg = Kwf_Controller_Action_Component_PagesController::getComponentNodeConfig($c, $user, $this->_acl);
        $this->assertFalse($cfg['actions']['delete']);
        $this->assertFalse($cfg['actions']['makeHome']);
        $this->assertTrue($cfg['actions']['add']);
        $this->assertTrue($cfg['allowDrop']);
        $this->assertFalse($cfg['allowDrag']);

        $c = Kwf_Component_Data_Root::getInstance()->getComponentById('1');
        $cfg = Kwf_Controller_Action_Component_PagesController::getComponentNodeConfig($c, $user, $this->_acl);
        $this->assertTrue($cfg['actions']['delete']);
        $this->assertTrue($cfg['actions']['makeHome']);
        $this->assertTrue($cfg['actions']['add']);
        $this->assertTrue($cfg['allowDrop']);
        $this->assertTrue($cfg['allowDrag']);

        $c = Kwf_Component_Data_Root::getInstance()->getComponentById('3');
        $cfg = Kwf_Controller_Action_Component_PagesController::getComponentNodeConfig($c, $user, $this->_acl);
        $this->assertTrue($cfg['actions']['delete']);
        $this->assertTrue($cfg['actions']['makeHome']);
        $this->assertTrue($cfg['actions']['add']);
        $this->assertTrue($cfg['allowDrop']);
        $this->assertTrue($cfg['allowDrag']);
    }
}
