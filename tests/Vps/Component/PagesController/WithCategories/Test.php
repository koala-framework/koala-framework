<?php
/**
 * @group PagesController
 * @group Vps_Component_Acl
 */
class Vps_Component_PagesController_WithCategories_Test extends Vpc_TestAbstract
{
    private $_acl;
    public function setUp()
    {
        parent::setUp('Vps_Component_PagesController_WithCategories_Root');
        $acl = new Vps_Acl();
        $this->_acl = $acl->getComponentAcl();
        $acl->addRole(new Zend_Acl_Role('test'));
        $this->_acl->allowComponent('test', null);
    }

    public function testNodeConfig()
    {
        $user = 'test';
        $c = Vps_Component_Data_Root::getInstance();
        $cfg = Vps_Controller_Action_Component_PagesController::getNodeConfig($c, $user, $this->_acl);
        $this->assertFalse($cfg['actions']['add']);
        $this->assertFalse($cfg['allowDrop']);
        $this->assertFalse($cfg['actions']['properties']);
        $this->assertFalse($cfg['actions']['delete']);
        $this->assertFalse($cfg['actions']['makeHome']);
        $this->assertFalse($cfg['allowDrag']);

        $c = Vps_Component_Data_Root::getInstance()->getComponentById('root-main');
        $cfg = Vps_Controller_Action_Component_PagesController::getNodeConfig($c, $user, $this->_acl);
        $this->assertFalse($cfg['actions']['properties']);
        $this->assertFalse($cfg['actions']['delete']);
        $this->assertFalse($cfg['actions']['makeHome']);
        $this->assertTrue($cfg['actions']['add']);
        $this->assertTrue($cfg['allowDrop']);
        $this->assertFalse($cfg['allowDrag']);

        $c = Vps_Component_Data_Root::getInstance()->getComponentById('1');
        $cfg = Vps_Controller_Action_Component_PagesController::getNodeConfig($c, $user, $this->_acl);
        $this->assertTrue($cfg['actions']['properties']);
        $this->assertTrue($cfg['actions']['delete']);
        $this->assertTrue($cfg['actions']['makeHome']);
        $this->assertTrue($cfg['actions']['add']);
        $this->assertTrue($cfg['allowDrop']);
        $this->assertTrue($cfg['allowDrag']);

        $c = Vps_Component_Data_Root::getInstance()->getComponentById('3');
        $cfg = Vps_Controller_Action_Component_PagesController::getNodeConfig($c, $user, $this->_acl);
        $this->assertTrue($cfg['actions']['properties']);
        $this->assertTrue($cfg['actions']['delete']);
        $this->assertTrue($cfg['actions']['makeHome']);
        $this->assertTrue($cfg['actions']['add']);
        $this->assertTrue($cfg['allowDrop']);
        $this->assertTrue($cfg['allowDrag']);
    }
}
