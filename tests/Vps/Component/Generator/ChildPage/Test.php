<?php
class Vps_Component_Generator_ChildPage_Test extends PHPUnit_Framework_TestCase
{
    private $_root;
    public function setUp()
    {
        Vps_Component_Data_Root::setComponentClass('Vps_Component_Generator_ChildPage_Root');
        $this->_root = Vps_Component_Data_Root::getInstance();
    }

    public function testSubpage()
    {
        $page = $this->_root->getChildComponent('-child')->getChildComponent('_1');
        $this->assertEquals('root-child_1', $page->dbId);
        
        $page = $this->_root->getChildComponent('-child')->getChildComponent(array('filename' => '1_foo'));
        $this->assertEquals('root-child_1', $page->dbId);

        $page = $this->_root->getChildPage(array('filename' => '1_foo'));
        $this->assertEquals('root-child_1', $page->dbId);
    }
}
