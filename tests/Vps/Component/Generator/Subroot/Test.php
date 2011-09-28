<?php
/**
 * @group Generator_Subroot
 */
class Vps_Component_Generator_Subroot_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vps_Component_Generator_Subroot_Root');
    }


    public function testComponentByClass()
    {
        $components = $this->_root->getComponentsByClass('Vpc_Basic_Image_Component');
        $this->assertEquals(2, count($components));

        $components = $this->_root->getComponentsByClass('Vps_Component_Generator_Subroot_Domain');
        $this->assertEquals(2, count($components));

        $components = $this->_root->getComponentsByClass('Vps_Component_Generator_Subroot_Static');
        $this->assertEquals(2, count($components));

        $c = $this->_root->getComponentById('6');

        $components = $this->_root->getComponentsByClass('Vpc_Basic_Image_Component', array('subroot' => $c));
        $this->assertEquals(1, count($components));

        $component = $this->_root->getComponentByClass('Vpc_Basic_Image_Component', array('subroot' => $c));
        $this->assertEquals(5, $component->componentId);

        $component = $this->_root->getComponentByClass('Vpc_Basic_Image_Component', array('limit' => 1));
        $this->assertEquals(4, $component->componentId);
    }

    /**
     * @expectedException Vps_Exception
     */
    public function testExceptionComponentByClass()
    {
        $this->_root->getComponentByClass('Vpc_Basic_Image_Component');
    }

    public function testComponentByDbId()
    {
        $components = $this->_root->getComponentsByDbId('test_1');
        $this->assertEquals(2, count($components));
        $this->assertEquals('root-at_static_1', $components[0]->componentId);
        $this->assertEquals('root-ch_static_1', $components[1]->componentId);

        $ch = $this->_root->getComponentById('root-ch_static_2');
        $at = $this->_root->getComponentById('root-at_static_2');

        $components = $this->_root->getComponentsByDbId('test_1', array('subroot' => $ch));
        $this->assertEquals(1, count($components));
        $this->assertEquals('root-ch_static_1', $components[0]->componentId);
        $components = $this->_root->getComponentsByDbId('test_1', array('subroot' => $at));
        $this->assertEquals('root-at_static_1', $components[0]->componentId);

        $component = $this->_root->getComponentByDbId('test_1', array('subroot' => $ch));
        $this->assertEquals('root-ch_static_1', $component->componentId);

        $component = $this->_root->getComponentByDbId('test_1', array('limit' => 1));
        $this->assertEquals('root-at_static_1', $component->componentId);

        $components = $this->_root->getComponentsByDbId('test_3');
        $this->assertEquals(1, count($components)); // siehe StaticGenerator
        $this->assertEquals('root-ch_static_3', $components[0]->componentId);
    }

    /**
     * @expectedException Vps_Exception
     */
    public function testExceptionComponentByDbId()
    {
        $this->_root->getComponentByDbId('test_1');
    }
}
