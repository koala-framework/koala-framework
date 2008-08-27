<?php
require_once dirname(__FILE__).'/../../../../tests/bootstrap.php';
class Vps_Component_Generator_GeneratorTest extends PHPUnit_Framework_TestCase
{
    public function testIsInstanceOf()
    {
        $this->assertTrue(is_instance_of('Vps_Component_Generator_PseudoPage_Table',
                                    'Vps_Component_Generator_PseudoPage_Interface'));
        $this->assertFalse(is_instance_of('Vps_Component_Generator_PseudoPage_Table',
                                    'Vps_Component_Generator_Page_Interface'));
        $this->assertTrue(is_instance_of('Vps_Component_Generator_Page_Table',
                                    'Vps_Component_Generator_Page_Interface'));
        $this->assertTrue(is_instance_of('Vps_Component_Generator_Page_Table',
                                    'Vps_Component_Generator_PseudoPage_Interface'));
        $this->assertFalse(is_instance_of('Vps_Component_Generator_Table',
                                    'Vps_Component_Generator_PseudoPage_Interface'));
        $this->assertFalse(is_instance_of('Vps_Component_Generator_Table',
                                    'Vps_Component_Generator_Page_Interface'));
        $this->assertFalse(is_instance_of('Vps_Component_Generator_Table',
                                    'Vps_Component_Generator_Box_Interface'));
    }

    public function testRoot()
    {
        $generators = Vps_Component_Generator_Abstract::getInstances('Vps_Component_Generator_Components_Root');
        $this->assertEquals(count($generators), 3);
        $this->assertTrue($generators[0] instanceof Vps_Component_Generator_Page);
        $this->assertTrue($generators[1] instanceof Vps_Component_Generator_Box_Static);
    }

    public function testRootConstraints()
    {
        $constraints = array('page' => true);
        $generators = Vps_Component_Generator_Abstract::getInstances('Vps_Component_Generator_Components_Root', $constraints);
        $this->assertEquals(count($generators), 1);
        $this->assertTrue($generators[0] instanceof Vps_Component_Generator_Page);

        $constraints = array('page' => false);
        $generators = Vps_Component_Generator_Abstract::getInstances('Vps_Component_Generator_Components_Root', $constraints);
        $this->assertEquals(count($generators), 2);
        $this->assertTrue($generators[0] instanceof Vps_Component_Generator_Box_Static);

        $constraints = array('box' => true);
        $generators = Vps_Component_Generator_Abstract::getInstances('Vps_Component_Generator_Components_Root', $constraints);
        $this->assertEquals(count($generators), 1);
        $this->assertTrue($generators[0] instanceof Vps_Component_Generator_Box_Static);

        $constraints = array('box' => true, 'page' => true);
        $generators = Vps_Component_Generator_Abstract::getInstances('Vps_Component_Generator_Components_Root', $constraints);
        $this->assertEquals(count($generators), 0);
    }

    public function testPlugin()
    {
        $generators = Vps_Component_Generator_Abstract::getInstances('Vps_Component_Generator_Components_PluginTest');
        $this->assertEquals(1, count($generators));
    }

    public function testDifferentGenerators()
    {
        $this->_assertGeneratorsCount(array(), 8);
        $this->_assertGeneratorsCount(array('page' => true), 2);
        $this->_assertGeneratorsCount(array('page' => false), 6);
        $this->_assertGeneratorsCount(array('pseudoPage' => true), 3);
        $this->_assertGeneratorsCount(array('pseudoPage' => true, 'page' => false), 1);
        $this->_assertGeneratorsCount(array('box' => true), 1);
        $this->_assertGeneratorsCount(array('box' => false), 7);
        $this->_assertGeneratorsCount(array('multiBox' => true), 1);
        $this->_assertGeneratorsCount(array('multiBox' => false), 7);
        $this->_assertGeneratorsCount(array('inherit' => true), 2);
        $this->_assertGeneratorsCount(array('unique' => true), 1);
        $this->_assertGeneratorsCount(array('generator' => 'static'), 2);
        $this->_assertGeneratorsCount(array('hasEditComponents' => true), 3);
    }

    private function _assertGeneratorsCount($constraints, $count)
    {
        $generators = Vps_Component_Generator_Abstract::getInstances('Vps_Component_Generator_Components_Multiple', $constraints);
        $this->assertEquals($count, count($generators));
    }

    public function testChildComponentClasses()
    {
        $this->_assertChildComponentClassesCount(array(), 5);
        $this->_assertChildComponentClassesCount(array('page' => true), 2);
        $this->_assertChildComponentClassesCount(array('page' => false), 5);
        $this->_assertChildComponentClassesCount(array('pseudoPage' => true), 2);
        $this->_assertChildComponentClassesCount(array('pseudoPage' => true, 'page' => false), 1);
        $this->_assertChildComponentClassesCount(array('box' => true), 1);
        $this->_assertChildComponentClassesCount(array('box' => false), 4);
        $this->_assertChildComponentClassesCount(array('multiBox' => true), 1);
        $this->_assertChildComponentClassesCount(array('multiBox' => false), 5);
        $this->_assertChildComponentClassesCount(array('inherit' => true), 2);
        $this->_assertChildComponentClassesCount(array('unique' => true), 1);
        $this->_assertChildComponentClassesCount(array('generator' => 'static'), 2);
        $this->_assertChildComponentClassesCount(array('hasEditComponents' => true), 2);
        $this->_assertChildComponentClassesCount(array('flags' => array('foo' => true)), 1);
        $this->_assertChildComponentClassesCount(array('generator' => 'pageTable', 'componentKey' => 'flag'), 1);
        $this->_assertChildComponentClassesCount(array('generator' => 'static', 'componentKey' => 'flag'), 0);
    }

    private function _assertChildComponentClassesCount($constraints, $count)
    {
        $classes = Vpc_Abstract::getChildComponentClasses('Vps_Component_Generator_Components_Multiple', $constraints);
        $this->assertEquals($count, count($classes));
    }
    
    public function testRecursiveComponentClasses()
    {
        $this->_assertRec(array(), 5);
        $this->_assertRec(array('page' => false), 4);
        $this->_assertRec(array('box' => true), 1);
    }

    private function _assertRec($constraints, $count)
    {
        $classes = Vpc_Abstract::getRecursiveChildComponentClasses(
            'Vps_Component_Generator_Components_Recursive', $constraints);
        $this->assertEquals($count, count($classes));
    }
    
    public function testChildComponents()
    {
        $root = Vps_Component_Data_Root::getInstance();
        $this->_assertChildComponents($root, array(), array('root-empty', 'root-static'));
        $this->_assertChildComponents($root, array('box' => true), array('root-empty'));
        $this->_assertChildComponents($root, array('page' => true), array());
        $multiple = $root->getChildComponent('-static');
        $this->assertEquals('root-static', $multiple->componentId);
        $this->_assertChildComponents($multiple, array('page' => true),
            array('root-static_pageStatic', 'root-static_1', 'root-static_2'));
        $this->_assertChildComponents($multiple, array('page' => true, 'flags' => array('foo'=>true)),
            array('root-static_pageStatic', 'root-static_2'));
    }

    public function _assertChildComponents($parent, $constraints, $componentIds)
    {
        $ids = array();
        foreach($parent->getChildComponents($constraints) as $cc) {
            $ids[] = $cc->componentId;
        }
        $this->assertEquals($componentIds, $ids);
    }
}
