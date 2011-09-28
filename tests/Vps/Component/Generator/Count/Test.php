<?php
class Vps_Component_Generator_Count_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vps_Component_Generator_Count_Root');
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
