<?php
class Vps_Component_Generator_Boxes_BoxesTest extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vps_Component_Generator_Boxes_Root');
    }

    public function testBoxes()
    {
        $page = $this->_root->getComponentById('1');
        $this->assertNotNull($page);
        $boxes = $page->getRecursiveChildComponents(array('box'=>true, 'page'=>false));
        $this->assertEquals(1, count($boxes));

        $classes = Vpc_Abstract::getIndirectChildComponentClasses('Vps_Component_Generator_Boxes_Root', array('inherit' => true));
        $this->assertEquals(array(), $classes);
    }

}
