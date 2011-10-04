<?php
/**
 * @group Update
 */
class Vps_Update_Tags_Test extends Vps_Test_TestCase
{

    public function testGetUpdatesSql()
    {
        $updates = Vps_Update::getUpdatesForDir('Vps/Update/Tags', 1, 1100);
        $this->assertEquals(2, count($updates));
        $this->assertEquals(100, $updates[0]->getRevision());
        $this->assertEquals(array('vps', 'foo', 'db'), $updates[0]->getTags());

        $this->assertEquals(101, $updates[1]->getRevision());
        $this->assertEquals(array('vps'), $updates[1]->getTags());
    }
}
