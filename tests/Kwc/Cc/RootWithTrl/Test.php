<?php
/**
 * @group Cc
 * @group Cc_RootWithTrl
 */
class Vpc_Cc_RootWithTrl_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vpc_Cc_RootWithTrl_Root');
    }

    public function testPages()
    {
        $root = Vps_Component_Data_Root::getInstance();
        $this->assertEquals(2, count($root->getChildComponents()));
        $this->assertNotNull($root->getComponentById('root-master-master'));
        $this->assertNotNull($root->getComponentById('root-master-slave'));
        $this->assertNotNull($root->getComponentById('root-slave-master'));
        $this->assertNotNull($root->getComponentById('root-slave-slave'));
        $this->assertNotNull($root->getComponentById('1'));
        $this->assertNotNull($root->getComponentById('root-master-slave-main_1'));
        $this->assertNotNull($root->getComponentById('root-slave-master-main_1'));
        $this->assertNotNull($root->getComponentById('root-slave-slave-main_1'));
    }

    public function testHome()
    {
        $root = Vps_Component_Data_Root::getInstance();
        $this->assertEquals('Vps_Component_Data_Home', get_class($root->getComponentById('1')));
        $this->assertEquals('Vps_Component_Data_Home', get_class($root->getComponentById('root-master-slave-main_1')));
        $this->assertEquals('Vps_Component_Data_Home', get_class($root->getComponentById('root-slave-master-main_1')));
        $this->assertEquals('Vps_Component_Data_Home', get_class($root->getComponentById('root-slave-slave-main_1')));
        $this->assertEquals('/vps/vpctest/Vpc_Cc_RootWithTrl_Root/master/master', $root->getComponentById('1')->url);
        $this->assertEquals('/vps/vpctest/Vpc_Cc_RootWithTrl_Root/master/slave', $root->getComponentById('root-master-slave-main_1')->url);
        $this->assertEquals('/vps/vpctest/Vpc_Cc_RootWithTrl_Root/slave/master', $root->getComponentById('root-slave-master-main_1')->url);
        $this->assertEquals('/vps/vpctest/Vpc_Cc_RootWithTrl_Root/slave/slave', $root->getComponentById('root-slave-slave-main_1')->url);
    }

    public function testChildPages()
    {
        $root = Vps_Component_Data_Root::getInstance();
        $this->assertEquals(3, count($root->getComponentById('root-master-master-main')->getChildPages()));
        $this->assertEquals(2, count($root->getComponentById('root-master-slave-main')->getChildPages()));
        $this->assertEquals(3, count($root->getComponentById('root-slave-master-main')->getChildPages()));
        $this->assertEquals(2, count($root->getComponentById('root-slave-slave-main')->getChildPages()));
        $this->assertEquals('3_trl', $root->getComponentById('root-master-slave-main_3')->filename);
    }
}
