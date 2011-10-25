<?php
/**
 * @group Kwc_InheritDifferentComponentClass
 */
class Kwf_Component_Generator_InheritDifferentComponentClass_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwf_Component_Generator_InheritDifferentComponentClass_Root');
    }

    public function testIt()
    {
        $this->assertContains('Kwf_Component_Generator_InheritDifferentComponentClass_Box_Inherit_Component', Kwc_Abstract::getComponentClasses());
        $this->assertEquals('Kwf_Component_Generator_InheritDifferentComponentClass_Box_Component', $this->_root->getChildComponent('-box')->componentClass);
        $c = $this->_root;
        $this->assertEquals('Kwf_Component_Generator_InheritDifferentComponentClass_Box_Component', $c->getChildComponent('-box')->componentClass);
        $this->assertEquals('boxnormal', $c->getChildComponent('-box')->render());
        $c = $c->getChildComponent('_page1');
        $this->assertEquals('Kwf_Component_Generator_InheritDifferentComponentClass_Box_Inherit_Component', $c->getChildComponent('-box')->componentClass);
        $this->assertEquals('boxinherit', $c->getChildComponent('-box')->render());
        $c = $c->getChildComponent('_page2');
        $this->assertEquals('Kwf_Component_Generator_InheritDifferentComponentClass_Box_Inherit_Component', $c->getChildComponent('-box')->componentClass);
        $this->assertEquals('boxinherit', $c->getChildComponent('-box')->render());
    }
}
