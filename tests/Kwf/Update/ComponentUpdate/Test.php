<?php
/**
 * @group Update
 */
class Kwf_Update_ComponentUpdate_Test extends Kwc_TestAbstract
{

    public function setUp()
    {
        parent::setUp('Kwf_Update_ComponentUpdate_Root');
        $this->_root = Kwf_Component_Data_Root::getInstance();
    }

    public function testFindKwcUpdates()
    {
        $updates = Kwf_Util_Update_Helper::getKwcUpdates(50, 110);
        $this->assertEquals(1, count($updates));
        $this->assertTrue($updates[0] instanceof Kwf_Update_ComponentUpdate_TestComponent_Update_100);

        $updates = Kwf_Util_Update_Helper::getKwcUpdates(50, 100);
        $this->assertEquals(0, count($updates));

        $updates = Kwf_Util_Update_Helper::getKwcUpdates(100, 110);
        $this->assertEquals(1, count($updates));

        $updates = Kwf_Util_Update_Helper::getKwcUpdates(101, 110);
        $this->assertEquals(0, count($updates));
    }
}
