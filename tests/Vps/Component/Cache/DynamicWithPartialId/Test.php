<?php
/**
 * @group Cache_DynamicWithPartialId
 */
class Vps_Component_Cache_DynamicWithPartialId_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vps_Component_Cache_DynamicWithPartialId_Root_Component');
    }

    public function testIt()
    {
        $c = $this->_root->getComponentById('root_test');
        $html = $c->render();
        $this->assertContains('Partial1-1 :: Partial2-2 :: Partial3-1 :: Partial4-2 :: Partial5-1', $html);

        Vps_Component_Cache_DynamicWithPartialId_TestComponent_Component::$ids = array(2, 4);
        $html = $c->render();
        $this->assertContains('Partial2-1 :: Partial4-2', $html);
    }
}
