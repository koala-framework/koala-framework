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
    }

    public function testIt()
    {
        $c = $this->_root->getComponentById(1);
        $this->assertEquals('Vps_Component_Generator_AlternativeComponent_DefaultComponent', $c->componentClass);

        $c = $this->_root->getComponentById(2);
        $this->assertEquals('Vps_Component_Generator_AlternativeComponent_AlternativeComponent', $c->componentClass);
    }
}
