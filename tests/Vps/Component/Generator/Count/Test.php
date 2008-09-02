<?php
class Vps_Component_Generator_Count_Test extends PHPUnit_Framework_TestCase
{
    private $_root;
    public function setUp()
    {
        Vps_Component_Data_Root::setComponentClass('Vps_Component_Generator_Count_Root');
        $this->_root = Vps_Component_Data_Root::getInstance();
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
