<?php
/**
 * @group Generator_GetChildIds
 */
class Vps_Component_Generator_GetChildIds_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vps_Component_Generator_GetChildIds_Root');
    }

    public function testByClass()
    {
        $this->assertEquals(array(1, 2), $this->_root->getChildIds(array('generator' => 'table')));
    }
}
