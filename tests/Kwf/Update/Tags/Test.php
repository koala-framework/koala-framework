<?php
/**
 * @group Update
 */
class Kwf_Update_Tags_Test extends Kwf_Test_TestCase
{

    public function testGetUpdatesSql()
    {
        $updates = Kwf_Update::getUpdatesForDir('Kwf/Update/Tags', 1, 1100);
        $this->assertEquals(2, count($updates));
        $this->assertEquals(100, $updates[0]->getRevision());
        $this->assertEquals(array('kwf', 'foo', 'db'), $updates[0]->getTags());

        $this->assertEquals(101, $updates[1]->getRevision());
        $this->assertEquals(array('kwf'), $updates[1]->getTags());
    }
}
