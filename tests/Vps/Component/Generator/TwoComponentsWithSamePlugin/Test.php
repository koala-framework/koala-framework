<?php
/**
 * @group Generator_TwoComponentsWithSamePlugin
 */
class Vps_Component_Generator_TwoComponentsWithSamePlugin_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vps_Component_Generator_TwoComponentsWithSamePlugin_Root');
    }

    public function testPlugin()
    {
        $components = $this->_root->getRecursiveChildComponents(array(
            'page' => false,
            'flags' => array('processInput' => true)
        ));
        $this->assertEquals(2, count($components));
    }
}
