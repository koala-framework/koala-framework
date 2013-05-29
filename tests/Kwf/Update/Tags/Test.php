<?php
/**
 * @group Update
 */
class Kwf_Update_Tags_Test extends Kwf_Test_TestCase
{
    public function setUp()
    {
        $this->_updateTagsValue= Kwf_Registry::get('config')->server->updateTags;
    }

    public function tearDown()
    {
        Kwf_Registry::get('config')->server->updateTags = $this->_updateTagsValue;
    }

    public function testGetUpdatesSql()
    {
        Kwf_Registry::get('config')->server->updateTags = array(
            'kwf', 'foo', 'db'
        );
        $updates = Kwf_Util_Update_Helper::getUpdatesForDir('Kwf/Update/Tags', 1, 1100);
        $this->assertEquals(2, count($updates));
        $this->assertEquals(100, $updates[0]->getRevision());
        $this->assertEquals(array('kwf', 'foo', 'db'), $updates[0]->getTags());

        $this->assertEquals(101, $updates[1]->getRevision());
        $this->assertEquals(array('kwf'), $updates[1]->getTags());
    }

    public function testGetUpdatesSql3()
    {
        Kwf_Registry::get('config')->server->updateTags = array(
            'kwf', 'db'
        );
        $updates = Kwf_Util_Update_Helper::getUpdatesForDir('Kwf/Update/Tags', 1, 1100);
        $this->assertEquals(1, count($updates));
        $this->assertEquals(101, $updates[0]->getRevision());
        $this->assertEquals(array('kwf'), $updates[0]->getTags());
    }
}
