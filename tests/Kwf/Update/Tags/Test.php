<?php
/**
 * @group Update
 */
class Kwf_Update_Tags_Test extends Kwf_Test_TestCase
{
    public function setUp()
    {
        $this->_updateTagsValue = Kwf_Registry::get('config')->server->updateTags;
        Kwf_Util_Update_Helper::clearUpdateTagsCache();
    }

    public function tearDown()
    {
        Kwf_Registry::get('config')->server->updateTags = $this->_updateTagsValue;
        Kwf_Util_Update_Helper::clearUpdateTagsCache();
    }

    public function testGetUpdatesSql()
    {
        Kwf_Registry::get('config')->server->updateTags = array(
            'kwf', 'foo'
        );
        $updates = Kwf_Util_Update_Helper::getUpdatesForDir('Kwf_Update_Tags_Update');
        $this->assertEquals(2, count($updates));
        $this->assertEquals(100, $updates[0]->getLegacyRevision());
        $this->assertEquals(array('kwf', 'foo'), $updates[0]->getTags());

        $this->assertEquals(101, $updates[1]->getLegacyRevision());
        $this->assertEquals(array('kwf'), $updates[1]->getTags());
    }

    public function testGetUpdatesSql3()
    {
        Kwf_Registry::get('config')->server->updateTags = array(
            'kwf'
        );
        $updates = Kwf_Util_Update_Helper::getUpdatesForDir('Kwf_Update_Tags_Update');
        $this->assertEquals(1, count($updates));
        $this->assertEquals(101, $updates[0]->getLegacyRevision());
        $this->assertEquals(array('kwf'), $updates[0]->getTags());
    }
}
