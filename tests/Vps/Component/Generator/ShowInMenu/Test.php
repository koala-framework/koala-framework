<?php
class Vps_Component_Generator_ShowInMenu_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vps_Component_Generator_ShowInMenu_Root');
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
