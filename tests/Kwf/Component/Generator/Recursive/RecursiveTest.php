<?php
class Kwf_Component_Generator_Recursive_RecursiveTest extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwf_Component_Generator_Recursive_Root');
    }

    public function testFlag()
    {
        $this->assertEquals(count(Kwc_Abstract::getIndirectChildComponentClasses('Kwf_Component_Generator_Recursive_Static',
            array('flags'=>array('foo'=>true)))), 1);

        $this->_assertRecursiveIds($this->_root->getChildComponent('_static'),
                                    array('flags'=>array('foo'=>true)),
                                    array('root_static-static2-flag'));

    }

    private function _assertRecursiveIds($component, $select, $ids)
    {
        $select = new Kwf_Component_Select($select);
        $initailSelect = clone $select;

        $c = $component->getRecursiveChildComponents($select);
        $foundIds = array();
        foreach ($c as $i) {
            $foundIds[] = $i->componentId;
        }
        $this->assertEquals($foundIds, $ids);
        $this->assertEquals($initailSelect, $select); //check if select was modified
    }

    public function testPages()
    {
        $c = $this->_root->getChildPages();
        $this->assertEquals(count($c), 1);
        $this->assertEquals(current($c)->componentId, 'root_static');

        $c = current($c)->getChildPages();
        $this->assertEquals(count($c), 1);
        $this->assertEquals(current($c)->componentId, 'root_static-static2_page');
    }

    public function testGetComponentByClass()
    {
        $components = Kwf_Component_Data_Root::getInstance()
            ->getComponentsByClass('Kwf_Component_Generator_Recursive_Root');
        $this->assertEquals(1, count($components));
        $components = Kwf_Component_Data_Root::getInstance()
            ->getComponentsBySameClass('Kwf_Component_Generator_Recursive_Root');
        $this->assertEquals(1, count($components));
        $generator = Kwf_Component_Generator_Abstract::getInstance('Kwf_Component_Generator_Recursive_Root', 'static');
        $this->assertEquals(1, count($generator->getChildData(null, array('componentClass'=>'Kwf_Component_Generator_Recursive_Static'))));
        $this->assertNotNull($this->_root->getComponentByClass('Kwf_Component_Generator_Recursive_Static'));
    }

}
