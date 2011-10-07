<?php
/**
 * @group Generator_GetChildIds
 */
class Kwf_Component_Generator_GetChildIds_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwf_Component_Generator_GetChildIds_Root');
    }

    public function testByClass()
    {
        $this->assertEquals(array(1, 2), $this->_root->getChildIds(array('generator' => 'table')));
    }
}
