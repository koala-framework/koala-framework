<?php
class Vpc_Directories_Top_Test extends PHPUnit_Framework_TestCase
{
    private $_root;
    public function setUp()
    {
        Vps_Component_Data_Root::setComponentClass('Vpc_Directories_Top_Root');
        $this->_root = Vps_Component_Data_Root::getInstance();
    }

    public function testDetail()
    {
        $dir = $this->_root->getComponentById('root_directory');
        $this->assertNotNull($dir);
        $details = $dir->getChildComponents(array('generator'=>'detail'));
        $this->assertEquals(count($details), 8);
    }

    public function testTop()
    {
        $dir = $this->_root->getComponentById('root_top');
        $this->assertNotNull($dir);
        $vars = $dir->getChildComponent('-view')->getComponent()->getTemplateVars();
        $this->assertEquals($vars['partialParams']['count'], 5);
        $paging = $dir->getChildComponent('-view')->getChildComponent('-paging');
        $this->assertEquals($paging->getComponent()->getCount(), 5);
    }
}
