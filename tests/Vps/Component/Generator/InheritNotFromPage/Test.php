<?php
/**
 * @group Generator
 * @group GeneratorInherit
 */
class Vps_Component_Generator_InheritNotFromPage_Test extends PHPUnit_Framework_TestCase
{
    private $_root;
    public function setUp()
    {
        Vps_Component_Data_Root::setComponentClass('Vps_Component_Generator_InheritNotFromPage_Root');
        $this->_root = Vps_Component_Data_Root::getInstance();
    }

    public function testTitleBox()
    {
        $c = $this->_root->getComponentById('root_page')->getChildComponents();
        $ids = array_keys($c);
        sort($ids);
        $this->assertEquals(array('root_page-box', 'root_page-child', 'root_page-title'), $ids);
    }

    public function testInheritedCorrectClass()
    {
        $c = $this->_root->getComponentById('root_page-child_page2')->getChildComponents();
        $ids = array_keys($c);
        sort($ids);
        $this->assertEquals(array('root_page-child_page2-box', 'root_page-child_page2-title'), $ids);
        $this->assertEquals($c['root_page-child_page2-box']->componentClass, 'Vps_Component_Generator_Inherit_Box');
    }
}
