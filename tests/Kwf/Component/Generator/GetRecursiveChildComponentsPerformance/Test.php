<?php
class Vps_Component_Generator_GetRecursiveChildComponentsPerformance_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vps_Component_Generator_GetRecursiveChildComponentsPerformance_Root');
    }

    public function testProcessInputPerformance()
    {
        Vps_Benchmark::enable();
        Vps_Benchmark::reset();
        $p = $this->_root->getChildComponent('_table1');

        $process = $p->getRecursiveChildComponents(array(
                    'page' => false,
                    'flags' => array('processInput' => true)
                ));
        $process = array_values($process);
        $this->assertEquals(1, count($process));
        $this->assertEquals('root_table1-1-1', $process[0]->componentId);

        //Vps_Benchmark::output();
        //root gibts schon
        //root_table, root_table-1, root_table-1-1
        $this->assertEquals(3, Vps_Benchmark::getCounterValue('componentDatas'));


        Vps_Benchmark::disable();
    }

    public function testStaticInTable()
    {
        $p = $this->_root->getComponentById('root_table2');
        $this->assertNotNull($p);
        $cmp = $p->getRecursiveChildComponents(array(
                    'componentClass' => 'Vps_Component_Generator_GetRecursiveChildComponentsPerformance_Static'
                ));
        $this->assertEquals(0, count($cmp));

        $p = $this->_root->getComponentById('root_table1');
        $this->assertNotNull($p);
        $cmp = $p->getRecursiveChildComponents(array(
                    'componentClass' => 'Vps_Component_Generator_GetRecursiveChildComponentsPerformance_Static'
                ));
        $this->assertEquals(1, count($cmp));
    }
}
