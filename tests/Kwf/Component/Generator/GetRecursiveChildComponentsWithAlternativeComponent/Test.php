<?php
/**
 * @group 
 */
class Kwf_Component_Generator_GetRecursiveChildComponentsWithAlternativeComponent_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwf_Component_Generator_GetRecursiveChildComponentsWithAlternativeComponent_Root');
    }

    public function test1()
    {
        $c = $this->_root->getRecursiveChildComponents(array(
            'componentClass' => 'Kwf_Component_Generator_GetRecursiveChildComponentsWithAlternativeComponent_TestComponent2_Component'
        ));
        $this->assertEquals(1, count($c));
        $c = array_values($c);
        $this->assertEquals('root-test1', $c[0]->componentId);
    }

    public function testChildComponentClassesIncludingAlternative()
    {
        $g = Kwf_Component_Generator_Abstract::getInstance('Kwf_Component_Generator_GetRecursiveChildComponentsWithAlternativeComponent_Root', 'test1');
        $c = $g->getChildComponentClasses();
        $this->assertEquals(2, count($c));
        $c = array_values($c);
        sort($c);
        $this->assertEquals(
            array(
                'Kwf_Component_Generator_GetRecursiveChildComponentsWithAlternativeComponent_TestComponent1_Component',
                'Kwf_Component_Generator_GetRecursiveChildComponentsWithAlternativeComponent_TestComponent2_Component',
        ), $c);
    }

    public function testGeneratorInstancesByChildComponentClass()
    {
        $s = array(
            'componentClass' => 'Kwf_Component_Generator_GetRecursiveChildComponentsWithAlternativeComponent_TestComponent2_Component'
        );
        $g = Kwf_Component_Generator_Abstract::getInstances('Kwf_Component_Generator_GetRecursiveChildComponentsWithAlternativeComponent_Root', $s);
        $this->assertEquals(1, count($g));
        $g = array_values($g);
        $this->assertEquals('Kwf_Component_Generator_GetRecursiveChildComponentsWithAlternativeComponent_Root', $g[0]->getClass());
        $this->assertEquals('test1', $g[0]->getGeneratorKey());
    }

    public function test2()
    {
        $c = $this->_root->getRecursiveChildComponents(array(
            'componentClass' => 'Kwc_Basic_None_Component'
        ));
        $this->assertEquals(1, count($c));
        $c = array_values($c);
        $this->assertEquals('root-test1-test2', $c[0]->componentId);
    }
}
