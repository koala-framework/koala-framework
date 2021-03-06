<?php
/**
 * @group PagesController
 * @group Kwf_Component_Acl
 */
class Kwf_Component_PagesController_PagesGeneratorActions_Test extends Kwc_TestAbstract
{
    private $_acl;
    public function setUp()
    {
        parent::setUp('Kwf_Component_PagesController_PagesGeneratorActions_Root');
        $acl = new Kwf_Acl();
        $this->_acl = $acl->getComponentAcl();

        $acl->addRole(new Zend_Acl_Role('test'));
        $this->_acl->allowComponent('test', null);

        $acl->addRole(new Zend_Acl_Role('special'));
        $this->_acl->allowComponent('special', 'Kwf_Component_PagesController_PagesGeneratorActions_SpecialComponent');
        $this->_acl->allowComponent('special', 'Kwf_Component_PagesController_PagesGeneratorActions_SpecialWithoutEditComponent');
    }

    public function testNodeConfig()
    {
        $user = 'test';
        $c = Kwf_Component_Data_Root::getInstance();
        $cfg = Kwf_Controller_Action_Component_PagesController::getComponentNodeConfig($c, $user, $this->_acl);
        $this->assertTrue($cfg['actions']['add']); //hinzufügen hier möglich weil  PageGenerator darunter
        $this->assertTrue($cfg['allowDrop']); //drop hier möglich weil PageGenerator darunter
        $this->assertFalse($cfg['actions']['delete']);
        $this->assertFalse($cfg['actions']['makeHome']);
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

    public function testOnlySpecial()
    {
        $user = 'special';
        $c = Kwf_Component_Data_Root::getInstance();
        $cfg = Kwf_Controller_Action_Component_PagesController::getComponentNodeConfig($c, $user, $this->_acl);
        $this->assertNotNull($cfg);
        $this->assertFalse($cfg['actions']['add']);
        $this->assertFalse($cfg['allowDrop']);
        $this->assertFalse($cfg['actions']['delete']);
        $this->assertFalse($cfg['actions']['makeHome']);
        $this->assertFalse($cfg['allowDrag']);
        $this->assertTrue($cfg['disabled']);

        $c = Kwf_Component_Data_Root::getInstance()->getComponentById('1');
        $cfg = Kwf_Controller_Action_Component_PagesController::getComponentNodeConfig($c, $user, $this->_acl);
        $this->assertNull($cfg);

        $c = Kwf_Component_Data_Root::getInstance()->getComponentById('3');
        $cfg = Kwf_Controller_Action_Component_PagesController::getComponentNodeConfig($c, $user, $this->_acl);
        $this->assertNull($cfg);

        $c = Kwf_Component_Data_Root::getInstance()->getComponentById('4');
        $cfg = Kwf_Controller_Action_Component_PagesController::getComponentNodeConfig($c, $user, $this->_acl);
        $this->assertNotNull($cfg);
        $this->assertTrue($cfg['actions']['add']);
        $this->assertTrue($cfg['allowDrop']);
        $this->assertTrue($cfg['actions']['delete']);
        $this->assertTrue($cfg['actions']['makeHome']);
        $this->assertTrue($cfg['allowDrag']);
        $this->assertFalse($cfg['disabled']);
    }

    public function testOnlySpecialInContainer()
    {
        $user = 'special';
        $c = Kwf_Component_Data_Root::getInstance()->getComponentById('5');
        $cfg = Kwf_Controller_Action_Component_PagesController::getComponentNodeConfig($c, $user, $this->_acl);
        $this->assertNotNull($cfg);
        $this->assertFalse($cfg['actions']['add']);
        $this->assertFalse($cfg['allowDrop']);
        $this->assertFalse($cfg['actions']['delete']);
        $this->assertFalse($cfg['actions']['makeHome']);
        $this->assertFalse($cfg['allowDrag']);
        $this->assertFalse($cfg['disabled']);
    }

    public function testEditComponents()
    {
        $user = 'test';
        $c = Kwf_Component_Data_Root::getInstance();
        $cfg = Kwf_Controller_Action_Component_PagesController::getComponentNodeConfig($c, $user, $this->_acl);
        $this->assertEquals(0, count($cfg['editComponents']));

        $c = Kwf_Component_Data_Root::getInstance()->getComponentById('1');
        $cfg = Kwf_Controller_Action_Component_PagesController::getComponentNodeConfig($c, $user, $this->_acl);
        $this->assertEquals(0, count($cfg['editComponents']));

        $c = Kwf_Component_Data_Root::getInstance()->getComponentById('3');
        $cfg = Kwf_Controller_Action_Component_PagesController::getComponentNodeConfig($c, $user, $this->_acl);
        $this->assertEquals(0, count($cfg['editComponents']));

        $c = Kwf_Component_Data_Root::getInstance()->getComponentById('4');
        $cfg = Kwf_Controller_Action_Component_PagesController::getComponentNodeConfig($c, $user, $this->_acl);
        $this->assertEquals(1, count($cfg['editComponents']));

        //SpecialContainer
        $c = Kwf_Component_Data_Root::getInstance()->getComponentById('5');
        $cfg = Kwf_Controller_Action_Component_PagesController::getComponentNodeConfig($c, $user, $this->_acl);
        $this->assertEquals(1, count($cfg['editComponents']));
    }

    public function testOnlySpecialEditComponents()
    {
        $user = 'special';
        $c = Kwf_Component_Data_Root::getInstance();
        $cfg = Kwf_Controller_Action_Component_PagesController::getComponentNodeConfig($c, $user, $this->_acl);
        $this->assertEquals(0, count($cfg['editComponents']));

        $c = Kwf_Component_Data_Root::getInstance()->getComponentById('1');
        $cfg = Kwf_Controller_Action_Component_PagesController::getComponentNodeConfig($c, $user, $this->_acl);
        $this->assertEquals(0, count($cfg['editComponents']));

        $c = Kwf_Component_Data_Root::getInstance()->getComponentById('3');
        $cfg = Kwf_Controller_Action_Component_PagesController::getComponentNodeConfig($c, $user, $this->_acl);
        $this->assertEquals(0, count($cfg['editComponents']));

        $c = Kwf_Component_Data_Root::getInstance()->getComponentById('4');
        $cfg = Kwf_Controller_Action_Component_PagesController::getComponentNodeConfig($c, $user, $this->_acl);
        $this->assertEquals(1, count($cfg['editComponents']));
    }

    public function testOnlySpecialInContainerEditComponents()
    {
        $user = 'special';
        $c = Kwf_Component_Data_Root::getInstance()->getComponentById('5');
        $cfg = Kwf_Controller_Action_Component_PagesController::getComponentNodeConfig($c, $user, $this->_acl);
        $this->assertEquals(1, count($cfg['editComponents']));
    }

    //für 7-special ist eine berechtigung da, allerdings keine extConfig. daher soll die seite 7 ganz
    //ausgeblendet werden - und die 6er daher auch
    public function testSpecialWithoutEditIsHidden()
    {
        $user = 'special';
        $c = Kwf_Component_Data_Root::getInstance()->getComponentById('6');
        $cfg = Kwf_Controller_Action_Component_PagesController::getComponentNodeConfig($c, $user, $this->_acl);
        $this->assertNull($cfg);

        $c = Kwf_Component_Data_Root::getInstance()->getComponentById('7');
        $cfg = Kwf_Controller_Action_Component_PagesController::getComponentNodeConfig($c, $user, $this->_acl);
        $this->assertNull($cfg);
    }
}
