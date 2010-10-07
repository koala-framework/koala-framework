<?php
/**
 * @group PagesController
 */
class Vps_Component_PagesController_PagesGeneratorActions_Test extends Vpc_TestAbstract
{
    private $_acl;
    public function setUp()
    {
        parent::setUp('Vps_Component_PagesController_PagesGeneratorActions_Root');
        $acl = new Vps_Acl();
        $this->_acl = $acl->getComponentAcl();

        $acl->addRole(new Zend_Acl_Role('test'));
        $this->_acl->allowComponent('test', null);

        $acl->addRole(new Zend_Acl_Role('special'));
        $this->_acl->allowComponent('special', 'Vps_Component_PagesController_PagesGeneratorActions_SpecialComponent');
    }

    public function testNodeConfig()
    {
        $user = 'test';
        $c = Vps_Component_Data_Root::getInstance();
        $cfg = Vps_Controller_Action_Component_PagesController::getNodeConfig($c, $user, $this->_acl);
        $this->assertTrue($cfg['actions']['add']); //hinzufügen hier möglich weil  PageGenerator darunter
        $this->assertTrue($cfg['allowDrop']); //drop hier möglich weil PageGenerator darunter
        $this->assertFalse($cfg['actions']['properties']);
        $this->assertFalse($cfg['actions']['delete']);
        $this->assertFalse($cfg['actions']['makeHome']);
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

    public function testOnlySpecial()
    {
        $user = 'special';
        $c = Vps_Component_Data_Root::getInstance();
        $cfg = Vps_Controller_Action_Component_PagesController::getNodeConfig($c, $user, $this->_acl);
        $this->assertNotNull($cfg);
        $this->assertFalse($cfg['actions']['add']);
        $this->assertFalse($cfg['allowDrop']);
        $this->assertFalse($cfg['actions']['properties']);
        $this->assertFalse($cfg['actions']['delete']);
        $this->assertFalse($cfg['actions']['makeHome']);
        $this->assertFalse($cfg['allowDrag']);
        $this->assertTrue($cfg['disabled']);

        $c = Vps_Component_Data_Root::getInstance()->getComponentById('1');
        $cfg = Vps_Controller_Action_Component_PagesController::getNodeConfig($c, $user, $this->_acl);
        $this->assertNull($cfg);

        $c = Vps_Component_Data_Root::getInstance()->getComponentById('3');
        $cfg = Vps_Controller_Action_Component_PagesController::getNodeConfig($c, $user, $this->_acl);
        $this->assertNull($cfg);

        $c = Vps_Component_Data_Root::getInstance()->getComponentById('4');
        $cfg = Vps_Controller_Action_Component_PagesController::getNodeConfig($c, $user, $this->_acl);
        $this->assertNotNull($cfg);
        $this->assertTrue($cfg['actions']['add']);
        $this->assertTrue($cfg['allowDrop']);
        $this->assertTrue($cfg['actions']['properties']);
        $this->assertTrue($cfg['actions']['delete']);
        $this->assertTrue($cfg['actions']['makeHome']);
        $this->assertTrue($cfg['allowDrag']);
        $this->assertFalse($cfg['disabled']);
    }


    //TODO editComponents (boxen usw)
}
