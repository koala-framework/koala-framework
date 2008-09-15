<?php
class Vps_Component_Generator_Boxes_BoxesTest extends PHPUnit_Framework_TestCase
{
    private $_root;
    public function setUp()
    {
        Vps_Component_Data_Root::setComponentClass('Vps_Component_Generator_Boxes_Root');
        $this->_root = Vps_Component_Data_Root::getInstance();
    }

    public function testBoxes()
    {
        $page = $this->_root->getComponentById('1');
        $boxes = $page->getRecursiveChildComponents(array('box'=>true, 'page'=>false));
        $this->assertEquals(1, count($boxes));

        $classes = Vpc_Abstract::getIndirectChildComponentClasses('Vps_Component_Generator_Boxes_Root', array('inherit' => true));
        $this->assertEquals(array(), $classes);
    }

}
