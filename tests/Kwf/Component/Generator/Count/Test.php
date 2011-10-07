<?php
class Kwf_Component_Generator_Count_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwf_Component_Generator_Count_Root');
    }

    public function testCount()
    {
        $dir = $this->_root->getComponentById('root_directory');
        $this->assertNotNull($dir);
        $details = $dir->getChildComponents(array('generator'=>'detail'));
        $this->assertEquals(count($details), 8);

        $count = $dir->countChildComponents(array('generator'=>'detail'));
        $this->assertEquals($count, 8);
    }
}
