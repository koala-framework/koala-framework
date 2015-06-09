<?php
/**
 * @group Update
 */
class Kwf_Update_Test extends Kwf_Test_TestCase
{
    public function setUp()
    {
        $this->_updateTagsValue= Kwf_Registry::get('config')->server->updateTags;
    }

    public function tearDown()
    {
        Kwf_Registry::get('config')->server->updateTags = $this->_updateTagsValue;
    }

    public function testUpdate()
    {
        $actions = array();
        $actions[0] = $this->getMock('Kwf_Update_Action_Abstract', array('preUpdate', 'postUpdate', 'update'));
        $actions[0]->expects($this->once())
                    ->method('update')
                    ->with();
        $actions[0]->expects($this->once())
                    ->method('preUpdate')
                    ->with();
        $actions[0]->expects($this->once())
                    ->method('postUpdate')
                    ->with();

        $update = new Kwf_Update_TestUpdate(123, 'abcd');
        $update->setActions($actions);
        $update->preUpdate();
        $update->update();
        $update->postUpdate();
    }

    public function testGetUpdatesForMultipleDir()
    {
        $updates = Kwf_Util_Update_Helper::getUpdatesForDir('Kwf_Update_UpdateMultiple_Update');
        $this->assertEquals(3, count($updates));
        $this->assertEquals(20, $updates[0]->getLegacyRevision());
        $this->assertEquals(100, $updates[1]->getLegacyRevision());
        $this->assertEquals(1000, $updates[2]->getLegacyRevision());
    }

    public function testGetUpdatesSql()
    {
        Kwf_Registry::get('config')->server->updateTags = array(
            'db'
        );
        $updates = Kwf_Util_Update_Helper::getUpdatesForDir('Kwf_Update_UpdateSql_Update');
        $this->assertEquals(1, count($updates));
        $this->assertEquals(100, $updates[0]->getLegacyRevision());
        $this->assertEquals('foo bar;', $updates[0]->sql);
    }
}
