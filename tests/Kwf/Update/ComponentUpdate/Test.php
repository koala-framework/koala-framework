<?php
/**
 * @group Update
 */
class Vps_Update_ComponentUpdate_Test extends Vpc_TestAbstract
{

    public function setUp()
    {
        parent::setUp('Vps_Update_ComponentUpdate_Root');
        $this->_root = Vps_Component_Data_Root::getInstance();
    }

    public function testFindVpcUpdates()
    {
        $updates = Vps_Update::getVpcUpdates(50, 110);
        $this->assertEquals(1, count($updates));
        $this->assertTrue($updates[0] instanceof Vps_Update_ComponentUpdate_TestComponent_Update_100);

        $updates = Vps_Update::getVpcUpdates(50, 100);
        $this->assertEquals(0, count($updates));

        $updates = Vps_Update::getVpcUpdates(100, 110);
        $this->assertEquals(1, count($updates));

        $updates = Vps_Update::getVpcUpdates(101, 110);
        $this->assertEquals(0, count($updates));
    }
}
