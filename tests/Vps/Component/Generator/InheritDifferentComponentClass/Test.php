<?php
/**
 * @group Vpc_InheritDifferentComponentClass
 */
class Vps_Component_Generator_InheritDifferentComponentClass_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vps_Component_Generator_InheritDifferentComponentClass_Root');
    }

    public function testIt()
    {
        $this->assertContains('Vps_Component_Generator_InheritDifferentComponentClass_Box_Inherit_Component', Vpc_Abstract::getComponentClasses());
        $this->assertEquals('Vps_Component_Generator_InheritDifferentComponentClass_Box_Component', $this->_root->getChildComponent('-box')->componentClass);
        $c = $this->_root;
        $this->assertEquals('Vps_Component_Generator_InheritDifferentComponentClass_Box_Component', $c->getChildComponent('-box')->componentClass);
        $this->assertEquals('boxnormal', $c->getChildComponent('-box')->render());
        $c = $c->getChildComponent('_page1');
        $this->assertEquals('Vps_Component_Generator_InheritDifferentComponentClass_Box_Inherit_Component', $c->getChildComponent('-box')->componentClass);
        $this->assertEquals('boxinherit', $c->getChildComponent('-box')->render());
        $c = $c->getChildComponent('_page2');
        $this->assertEquals('Vps_Component_Generator_InheritDifferentComponentClass_Box_Inherit_Component', $c->getChildComponent('-box')->componentClass);
        $this->assertEquals('boxinherit', $c->getChildComponent('-box')->render());
    }
}
