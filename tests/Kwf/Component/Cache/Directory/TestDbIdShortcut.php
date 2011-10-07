<?php
/**
 * @group Component_Cache_Directory
 * @group Component_Cache_Directory_DbIdShortcut
 */
class Kwf_Component_Cache_Directory_TestDbIdShortcut extends Kwc_TestAbstract
{
    public function setUp()
    {
        $this->markTestIncomplete('eventscache');

        parent::setUp('Kwf_Component_Cache_Directory_DbIdShortcut_Component');
    }

    public function testDirectory()
    {
        $dir = $this->_root;
        $dirModel = $dir->getComponent()->getChildModel();

        $this->assertEquals('1 d1', $dir->render());

        $dirModel->import(Kwf_Model_Abstract::FORMAT_ARRAY, array(
            array('id' => 2, 'content' => 'd2')
        ));
        $this->_process();
        $this->assertEquals('2 d1d2', $dir->render());
    }
}
