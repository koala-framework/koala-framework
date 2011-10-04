<?php
/**
 * @group Generator
 * @group Generator_AlternativeComponent
 */
class Vps_Component_Generator_AlternativeComponent_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vps_Component_Generator_AlternativeComponent_Root');
        /*
        root
        _1 (composite)
          -child (default)
          _2 (composite)
            -child (alternative)
         */
    }

    public function testClasses()
    {
        $c = $this->_root->getComponentById('1-child');
        $this->assertEquals('Vps_Component_Generator_AlternativeComponent_Default_Component', $c->componentClass);

        $c = $this->_root->getComponentById('2-child');
        $this->assertEquals('Vps_Component_Generator_AlternativeComponent_Alternative_Component', $c->componentClass);
    }

    public function testRender()
    {
        $c = $this->_root->getComponentById('1-child');
        $this->assertEquals('1-child', $c->render());

        $c = $this->_root->getComponentById('2-child');
        $this->assertEquals('1-child', $c->render());
    }
}
