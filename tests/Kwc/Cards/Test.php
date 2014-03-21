<?php
/**
 */
class Kwc_Cards_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwc_Cards_Root');
    }

    public function testLinkTagComponentClass()
    {
        $component = Kwf_Component_Data_Root::getInstance()->getComponentById('root_cards1');
        $c = $component->getChildComponent('-cards')->getChildComponent('-child');
        $this->assertEquals($c->componentClass, 'Kwc_Cards_Sub2_Component');
    }

    public function testLinkTagRecursiveChildComponents()
    {
        $component = Kwf_Component_Data_Root::getInstance()->getComponentById('root_cards1');
        $childComponents = $component->getRecursiveChildComponents(
            array('componentClass' => 'Kwc_Cards_Sub1_Component', 'ignoreVisible'=>true)
        );
        $this->assertEquals(0, count($childComponents));
    }
}
