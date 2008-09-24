<?php
class Vps_Component_Generator_ShowInMenu_Test extends PHPUnit_Framework_TestCase
{
    private $_root;
    public function setUp()
    {
        Vps_Component_Data_Root::setComponentClass('Vps_Component_Generator_ShowInMenu_Root');
        $this->_root = Vps_Component_Data_Root::getInstance();
    }

    public function testShowInMenu()
    {
        $this->_assertIds($this->_root->getChildPages(array('showInMenu'=>true)),
                            array('root_page2'));
        $this->_assertIds($this->_root->getChildPages(array('showInMenu'=>false)),
                            array('root_page3'));
    }

    private function _assertIds($components, $ids)
    {
        $i = array();
        foreach ($components as $c) {
            $i[] = $c->componentId;
        }
        $this->assertEquals($i, $ids);
    }
}
