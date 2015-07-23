<?php
/**
 * @group Component_Cache_Directory
 */
class Kwf_Component_Cache_Directory_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwf_Component_Cache_Directory_Root_Component');
        /*
        root
         _dir (kein itemPage, keine Einträge, ist trlRoot)
           -view (gibt "{count} {$item[0]->content} {$item[1]->content} ..." aus)
         _list (hat dir als directory)
           -view (erbt von _dir-view)
         _trldir (basiert auf _dir, ist trlRoot, hat eigenes Model)
           -view (ist wie immer bei trldir gleiche Komponente wie _dir-view)
        */
    }

    public function testImportUpdate()
    {
        $root = $this->_root;
        $dir = $root->getChildComponent('_dir');
        $dirModel = $dir->getComponent()->getChildModel();

        $this->assertEquals('0 ', $dir->render());

        $dirModel->import(Kwf_Model_Abstract::FORMAT_ARRAY, array(
            array('id' => 1, 'component_id' => 'root_dir', 'content' => 'd1'),
            array('id' => 2, 'component_id' => 'root_dir', 'content' => 'd2')
        ));
        $this->_process();
        $this->assertEquals('2 d1d2', $dir->render());
    }

    public function testDirectory1()
    {
        $root = $this->_root;
        $dir = $root->getChildComponent('_dir');
        $list = $root->getChildComponent('_list');
        $dirModel = $dir->getComponent()->getChildModel();
        $cacheModel = Kwf_Component_Cache::getInstance()->getModel();

        $this->assertEquals('0 ', $dir->render());
        $this->assertEquals('0 ', $list->render());

        $row = $dirModel->createRow(array(
            'id' => 1, 'component_id' => 'root_dir', 'content' => 'd1'
        ));
        $row->save();
        $row = $dirModel->createRow(array(
            'id' => 2, 'component_id' => 'root_dir', 'content' => 'd2'
        ));
        $row->save();
        $this->_process();
        $this->assertEquals('2 d1d2', $dir->render());
        $this->assertEquals('2 d1d2', $list->render());

        // Bei Partial_Id darf im eigene Directory nur eine Row gelöscht werden
        $row = $dirModel->getRow(1);
        $row->content = 'foo';
        $row->save();
        $this->_process();

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

        $this->assertEquals('2 food2', $dir->render());
        $this->assertEquals('2 food2', $list->render());
    }

    public function testTrl()
    {
        $root = $this->_root;
        $dir = $root->getChildComponent('_dir');
        $trldir = $root->getChildComponent('_trldir');
        $dirModel = $dir->getComponent()->getChildModel();
        $trlModel = $trldir->getComponent()->getChildModel();

        $this->assertEquals('0 ', $trldir->render());

        $dirModel->import(Kwf_Model_Abstract::FORMAT_ARRAY, array(
            array('id' => 1, 'component_id' => 'root_dir', 'content' => 'd1'),
            array('id' => 2, 'component_id' => 'root_dir', 'content' => 'd2')
        ));
        $trlModel->import(Kwf_Model_Abstract::FORMAT_ARRAY, array(
            array('component_id' => 'root_trldir-1', 'visible' => false),
            array('component_id' => 'root_trldir-2', 'content' => 'foo', 'visible' => true)
        ));
        $this->_process();
        $this->assertEquals('1 foo', $trldir->render());

        $row = $trlModel->getRow('root_trldir-2');
        $row->content = 'bar';
        $row->save();
        $this->_process();
        $this->assertEquals('1 bar', $trldir->render());
    }

    public function testAllChainedByMaster()
    {
        $chained = Kwc_Chained_Abstract_Component::getAllChainedByMaster($this->_root->getChildComponent('_dir'), 'Trl');
        $this->assertEquals(1, count($chained));
    }
}
