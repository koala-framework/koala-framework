<?php
class Vps_Component_Generator_Recursive_RecursiveTest extends PHPUnit_Framework_TestCase
{
    private $_root;
    public function setUp()
    {
        Vps_Registry::get('config')->vpc->rootComponent = 'Vps_Component_Generator_Recursive_Root';
        $this->_root = Vps_Component_Data_Root::getInstance();
    }

    public function testFlag()
    {
        $this->assertEquals(count(Vpc_Abstract::getRecursiveChildComponentClasses('Vps_Component_Generator_Recursive_Static',
            array('flags'=>array('foo'=>true)))), 2);

        $c = $this->_root->getChildComponent('_static')->getRecursiveChildComponents(array('flags'=>array('foo'=>true)));
        $this->assertEquals(count($c), 1);
        $this->assertEquals(current($c)->componentId, 'root_static-static2-flag');
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

}
