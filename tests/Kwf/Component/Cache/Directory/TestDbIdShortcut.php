<?php
/**
 * @group Component_Cache_Directory
 * @group Component_Cache_Directory_DbIdShortcut
 */
class Kwf_Component_Cache_Directory_TestDbIdShortcut extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwf_Component_Cache_Directory_DbIdShortcut_Component');
    }

    public function testDirectory()
    {
        $dir = $this->_root;
        $dirModel = $dir->getComponent()->getChildModel();

        $this->assertEquals('1 d1', $dir->render());

        $row = $dirModel->createRow(array('id' => 2, 'content' => 'd2'));
        $row->save();
        $this->_process();
        $this->assertEquals('2 d1d2', $dir->render());
    }
}
