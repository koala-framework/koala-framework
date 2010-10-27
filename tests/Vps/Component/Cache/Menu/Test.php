<?php
/**
 * @group Component_Cache_Menu
 */
class Vps_Component_Cache_Menu_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vps_Component_Cache_Menu_Root_Component');
    }

    public function testMenu()
    {
        $root = $this->_root;

        $this->assertEquals('1-menu f1,f4', $root->getComponentById(1)->render(true, true));
        $this->assertEquals('1-menu f1,f4', $root->getComponentById(2)->render(true, true));
        $this->assertEquals('1-menu f1,f4', $root->getComponentById(3)->render(true, true));

        $row = $root->getGenerator('page')->getModel()->getRow(1);
        $row->name = 'g1';
        $row->save();
        $this->_process();
        $this->assertEquals('1-menu g1,f4', $root->getComponentById(1)->render(true, true));
        $this->assertEquals('1-menu g1,f4', $root->getComponentById(2)->render(true, true));
        $this->assertEquals('1-menu g1,f4', $root->getComponentById(3)->render(true, true));

        $row = $root->getGenerator('page')->getModel()->getRow(4);
        $row->name = 'g4';
        $row->save();
        $this->_process();
        $this->assertEquals('1-menu g1,g4', $root->getComponentById(1)->render(true, true));
        $this->assertEquals('1-menu g1,g4', $root->getComponentById(2)->render(true, true));
        $this->assertEquals('1-menu g1,g4', $root->getComponentById(3)->render(true, true));
    }
}
