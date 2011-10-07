<?php
class Vps_Component_Generator_ChildPage_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vps_Component_Generator_ChildPage_Root');
        Vps_Benchmark::enable();
        Vps_Benchmark::reset();
    }

    public function tearDown()
    {
        parent::tearDown();
        Vps_Benchmark::disable();
    }

    public function testComponentClassConstraint()
    {
        $c = $this->_root->getComponentById('root-child');
        $this->assertNotNull($c);

        $c = $this->_root->getComponentById('root-child', array('componentClass'=>'Vps_Component_Generator_ChildPage_Child'));
        $this->assertNotNull($c);

        $c = $this->_root->getComponentById('root-child', array('componentClass'=>'NotExistent'));
        $this->assertNull($c);

        $c = $this->_root->getComponentById('root-child_1', array('componentClass'=>'Vpc_Basic_Empty_Component'));
        $this->assertNotNull($c);
    }

    public function testSubpage()
    {
        $this->assertNotNull($this->_root->getChildComponent('-child'));
        $page = $this->_root->getChildComponent('-child')
                            ->getChildComponent('_1');
        $this->assertEquals('root-child_1', $page->dbId);
        /*
        $this->assertEquals(Vps_Benchmark::getCounterValue('generators'), 2);
        $this->assertEquals(Vps_Benchmark::getCounterValue('componentDatas'), 2);
        $this->assertEquals(Vps_Benchmark::getCounterValue('getChildComponents'), 3);
        */

        $page = $this->_root->getChildComponent('-child')->getChildComponent(array('filename' => '1_foo'));
        $this->assertEquals('root-child_1', $page->dbId);

        $page = $this->_root->getChildPage(array('filename' => '1_foo'));
        $this->assertEquals('root-child_1', $page->dbId);
    }

    public function testSubpageForm()
    {
        $formSelect = array(
            'page' => false,
            'flags' => array('processInput' => true)
        );
        $forms = $this->_root->getRecursiveChildComponents($formSelect);
        $this->assertEquals(1, count($forms));
        $this->assertEquals('root-form', current($forms)->dbId);
        /*
        $this->assertEquals(Vps_Benchmark::getCounterValue('generators'), 3);
        $this->assertEquals(Vps_Benchmark::getCounterValue('componentDatas'), 1);
        $this->assertEquals(Vps_Benchmark::getCounterValue('getChildComponents'), 2);
        $this->assertEquals(Vps_Benchmark::getCounterValue('getRecursiveChildComponents'), 1);
        */
    }
    public function testSubpageForm2()
    {
        $formSelect = array(
            'page' => false,
            'flags' => array('processInput' => true)
        );
        //$c = $this->_root->getChildComponent('-child')->getChildComponent('_1');
        $c = $this->_root->getComponentById('root-child_1');
//         d($c->getChildComponents());
        $forms = $c->getRecursiveChildComponents($formSelect);
        $this->assertEquals(1, count($forms));
        $this->assertEquals('root-child_1-form', current($forms)->dbId);
        /*
        $this->assertEquals(Vps_Benchmark::getCounterValue('generators'), 3);
        $this->assertEquals(Vps_Benchmark::getCounterValue('componentDatas'), 3);
        $this->assertEquals(Vps_Benchmark::getCounterValue('getChildComponents'), 4);
        $this->assertEquals(Vps_Benchmark::getCounterValue('getRecursiveChildComponents'), 1);
        */
    }
}
