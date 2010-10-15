<?php
/**
 * @group Component_Cache_Directory
 */
class Vps_Component_Cache_Directory_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vps_Component_Cache_Directory_Root_Component');
    }

    public function testDirectory()
    {
        $root = $this->_root;
        $dir = $root->getChildComponent('_dir');
        $list = $root->getChildComponent('_list');
        $dirModel = $dir->getComponent()->getChildModel();

        $this->assertEquals('', $dir->render());
        $this->assertEquals('', $list->render());

        $dirModel->import(Vps_Model_Abstract::FORMAT_ARRAY, array(
            array('id' => 1, 'component_id' => 'root_dir', 'content' => 'd1'),
            array('id' => 2, 'component_id' => 'root_dir', 'content' => 'd2')
        ));
        $this->_process();
        $this->assertEquals('d1d2', $dir->render());
        $this->assertEquals('d1d2', $list->render());

        $row = $dirModel->getRow(1);
        $row->content = 'foo';
        $row->save();
        $this->_process();

        // Bei Partial_Id darf im eigene Directory nur eine Row gelöscht werden
        $cacheModel = Vps_Component_Cache::getInstance()->getModel();
        $select = $cacheModel->select()
            ->whereEquals('component_id', 'root_list-view')
            ->whereEquals('type', 'partial');
        $rows = $cacheModel->getRows($select);
        $this->assertEquals(2, $rows->count());
        $this->assertEquals(1, $rows->current()->value);
        $this->assertEquals(true, $rows->current()->deleted);
        $rows->next();
        $this->assertEquals(2, $rows->current()->value);
        $this->assertEquals(false, $rows->current()->deleted);

        $this->assertEquals('food2', $dir->render());
        $this->assertEquals('food2', $list->render());
    }
}
