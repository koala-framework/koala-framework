<?php
/**
 * @group Kwc
 * @group Kwc_ExpandedComponentId
 */
class Kwf_Component_ExpandedComponentId_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwf_Component_ExpandedComponentId_Root');
    }

    public function testIds()
    {
        $c = $this->_root;
        $this->assertEquals('root', $c->getExpandedComponentId());

        $c = $c->getChildComponent('-main');
        $this->assertEquals('root-main', $c->getExpandedComponentId());

        $c = $c->getChildComponent('1');
        $this->assertEquals('root-main_1', $c->getExpandedComponentId());

        $c = $c->getChildComponent('2');
        $this->assertEquals('root-main_1_2', $c->getExpandedComponentId());

        $c = $c->getChildComponent('-bar');
        $this->assertEquals('root-main_1_2-bar', $c->getExpandedComponentId());

        $c = $c->getChildComponent('_foo');
        $this->assertEquals('root-main_1_2-bar_foo', $c->getExpandedComponentId());
    }
}
