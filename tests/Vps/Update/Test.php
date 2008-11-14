<?php
/**
 * @group Update
 */
class Vps_Update_Test extends PHPUnit_Framework_TestCase
{
    public function testUpdate()
    {
        $actions = array();
        $actions[0] = $this->getMock('Vps_Update_Action_Abstract', array('preUpdate', 'postUpdate', 'update'));
        $actions[0]->expects($this->once())
                    ->method('update')
                    ->with();
        $actions[0]->expects($this->once())
                    ->method('preUpdate')
                    ->with();
        $actions[0]->expects($this->once())
                    ->method('postUpdate')
                    ->with();

        $update = new Vps_Update_TestUpdate(123);
        $update->setActions($actions);
        $update->preUpdate();
        $update->update();
        $update->postUpdate();
    }

    public function testGetUpdatesForDir()
    {
        $updates = Vps_Update::getUpdatesForDir('Vps/Update/UpdateDir', 50, 110);
        $this->assertEquals(1, count($updates));
        $this->assertTrue($updates[0] instanceof Vps_Update_UpdateDir_Update_100);

        $updates = Vps_Update::getUpdatesForDir('Vps/Update/UpdateDir', 50, 100);
        $this->assertEquals(0, count($updates));

        $updates = Vps_Update::getUpdatesForDir('Vps/Update/UpdateDir', 50, 101);
        $this->assertEquals(1, count($updates));

        $updates = Vps_Update::getUpdatesForDir('Vps/Update/UpdateDir', 99, 101);
        $this->assertEquals(1, count($updates));

        $updates = Vps_Update::getUpdatesForDir('Vps/Update/UpdateDir', 100, 101);
        $this->assertEquals(1, count($updates));

        $updates = Vps_Update::getUpdatesForDir('Vps/Update/UpdateDir', 101, 110);
        $this->assertEquals(0, count($updates));
    }
}
