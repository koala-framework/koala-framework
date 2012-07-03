<?php
/**
 * @group Component_Cache_Paging
 */
class Kwf_Component_Cache_Paging_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwf_Component_Cache_Paging_Root');
    }

    public function testChangeDirPagingCount()
    {
        $dir = $this->_root->getChildComponent('_dir');
        $dirModel = $dir->getComponent()->getChildModel();

        $this->assertEquals('12n', $dir->render());

        $dirModel->getRow(1)->delete();
        $dirModel->getRow(2)->delete();
        $this->_process();

        $this->assertEquals('1', $dir->render());

        $dirModel->createRow(array('id' => 4))->save();
        $dirModel->createRow(array('id' => 5))->save();
        $dirModel->createRow(array('id' => 6))->save();
        $dirModel->createRow(array('id' => 7))->save();
        $this->_process();
        $this->assertEquals('123nl', $dir->render());
    }
}
