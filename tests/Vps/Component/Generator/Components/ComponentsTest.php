<?php
/**
 * @group Vpc_UrlResolve
 */
class Vps_Component_Generator_Components_ComponentsTest extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vps_Component_Generator_Components_Root');
    }

    public function testRoot()
    {
        $generators = Vps_Component_Generator_Abstract::getInstances('Vps_Component_Generator_Components_Root');
        $this->assertEquals(count($generators), 3);
        $this->assertTrue($generators[1] instanceof Vpc_Root_Category_Generator);
        $this->assertTrue($generators[0] instanceof Vps_Component_Generator_Box_Static);
    }

    public function testRootConstraints()
    {
        $constraints = array('generator' => 'static');
        $generators = Vps_Component_Generator_Abstract::getInstances('Vps_Component_Generator_Components_Root', $constraints);
        $this->assertEquals(count($generators), 1);
        $this->assertTrue($generators[0] instanceof Vps_Component_Generator_Static);

        $constraints = array('page' => true);
        $generators = Vps_Component_Generator_Abstract::getInstances('Vps_Component_Generator_Components_Root', $constraints);
        $this->assertEquals(count($generators), 1);
        $this->assertTrue($generators[0] instanceof Vpc_Root_Category_Generator);

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

        $constraints = array('generatorClass' => 'Vps_Component_Generator_Static');
        $generators = Vps_Component_Generator_Abstract::getInstances('Vps_Component_Generator_Components_Root', $constraints);
        $this->assertEquals(count($generators), 2);
    }

    public function testPlugin()
    {
        Vps_Component_Data_Root::setComponentClass('Vps_Component_Generator_Components_PluginTest'); //ist eigentlich keine root, aber wegen settings cache trotzdem setzen
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
        $this->_assertGeneratorsCount(array('generator' => 'static'), 1);
        $this->_assertGeneratorsCount(array('generator' => 'pluginStatic'), 0);
        $this->_assertGeneratorsCount(array('generator' => 'page'), 0);
        $this->_assertGeneratorsCount(array('hasEditComponents' => true), 3);
        $this->_assertGeneratorsCount(array('componentClasses' => array(
            'Vps_Component_Generator_Components_Multiple', 'Vpc_Basic_Html_Component'
        )), 1);
        $this->_assertGeneratorsCount(array('componentClasses' => array(
            'Vps_Component_Generator_Components_Multiple', 'Vpc_Basic_Html_Component'
        )), 2, 'Vps_Component_Generator_Components_Root');
        $this->_assertGeneratorsCount(array('componentClasses' => array(
            'Vps_Component_Generator_Components_Multiple', 'Vpc_Basic_Empty_Component'
        )), 3, 'Vps_Component_Generator_Components_Root');
    }

    private function _assertGeneratorsCount($select, $count, $component = 'Vps_Component_Generator_Components_Multiple')
    {
        $select = new Vps_Component_Select($select);
        $initailSelect = clone $select;
        $generators = Vps_Component_Generator_Abstract::getInstances($component, $select);
        $this->assertEquals($count, count($generators));
        $this->assertEquals($initailSelect, $select); //check if select was modified
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
        $this->_assertChildComponentClassesCount(array('generator' => 'static'), 1);
        $this->_assertChildComponentClassesCount(array('generator' => 'pluginStatic'), 0);
        $this->_assertChildComponentClassesCount(array('hasEditComponents' => true), 2);
        $this->_assertChildComponentClassesCount(array('flags' => array('foo' => true)), 1);
        $this->_assertChildComponentClassesCount(array('generator' => 'pageTable', 'componentKey' => 'flag'), 1);
        $this->_assertChildComponentClassesCount(array('generator' => 'static', 'componentKey' => 'flag'), 0);
    }

    private function _assertChildComponentClassesCount($select, $count)
    {
        $select = new Vps_Component_Select($select);
        $initailSelect = clone $select;
        $classes = Vpc_Abstract::getChildComponentClasses('Vps_Component_Generator_Components_Multiple', $select);
        $this->assertEquals($count, count($classes));
        $this->assertEquals($initailSelect, $select); //check if select was modified
    }

    public function testRecursiveComponentClasses()
    {
        Vps_Component_Data_Root::setComponentClass('Vps_Component_Generator_Components_Recursive'); //ist eigentlich keine root, aber wegen settings cache trotzdem setzen
        $this->_assertRec(array(), 2);
        $this->_assertRec(array('page' => false), 2);
        $this->_assertRec(array('box' => true), 1);
    }

    private function _assertRec($constraints, $count)
    {
        $classes = Vpc_Abstract::getIndirectChildComponentClasses(
            'Vps_Component_Generator_Components_Recursive', $constraints);
        $this->assertEquals($count, count($classes));
    }

    public function testChildComponents()
    {
        $root = $this->_root;
        $this->_assertChildComponents($root, array(), array('root-empty', '1', 'root-static'));
        $this->_assertChildComponents($root, array('box' => true), array('root-empty'));
        $this->_assertChildComponents($root, array('page' => true), array('1'));
        $multiple = $root->getChildComponent('-static');
        $this->assertEquals('root-static', $multiple->componentId);
        $this->_assertChildComponents($multiple, array('page' => true),
            array('root-static_pageStatic', 'root-static_1', 'root-static_2'));
        $this->_assertChildComponents($multiple, array('page' => true, 'flags' => array('foo'=>true)),
            array('root-static_pageStatic', 'root-static_2'));
        $this->_assertChildComponents($multiple, array('generator' => 'pageTable'),
            array('root-static_1', 'root-static_2'));
        $this->_assertChildComponents($root->getChildComponent('1'), array('generator' => 'pageTable'),
            array('1_1', '1_2'));
    }

    public function testHome()
    {
        $p = $this->_root->getPageByUrl('http://'.Zend_Registry::get('config')->server->domain.'/', null);
        $this->assertEquals($p->componentId, '1');
    }


    public function _assertChildComponents($parent, $select, $componentIds)
    {
        $select = new Vps_Component_Select($select);
        $initailSelect = clone $select;
        $ids = array();
        foreach($parent->getChildComponents($select) as $cc) {
            $ids[] = $cc->componentId;
        }
        $this->assertEquals($componentIds, $ids);
        $this->assertEquals($initailSelect, $select); //check if select was modified
    }
}
