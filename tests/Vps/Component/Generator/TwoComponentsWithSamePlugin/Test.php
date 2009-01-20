<?php
/**
 * @group Generator_TwoComponentsWithSamePlugin
 */
class Vps_Component_Generator_TwoComponentsWithSamePlugin_Test extends PHPUnit_Framework_TestCase
{
    private $_root;
    public function setUp()
    {
        Vps_Component_Data_Root::setComponentClass('Vps_Component_Generator_TwoComponentsWithSamePlugin_Root');
        $this->_root = Vps_Component_Data_Root::getInstance();
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
