<?php
/**
 * @group Generator_Plugin
 * @author Franz
 */
class Vps_Component_Generator_Plugin_Test extends PHPUnit_Framework_TestCase
{
    private $_root;
    public function setUp()
    {
        Vps_Component_Data_Root::setComponentClass('Vps_Component_Generator_Plugin_Root');
        $this->_root = Vps_Component_Data_Root::getInstance();
    }

    public function testPlugin()
    {
        $this->markTestIncomplete();

        $generators = Vps_Component_Generator_Abstract::getInstances(
            'Vps_Component_Generator_Plugin_Static',
            array('componentClasses' => array('Vps_Component_Plugin_Password_Component'))
        );
        $this->assertEquals(1, count($generators));

        $components = $this->_root->getRecursiveChildComponents(array(
            'componentClasses' => array('Vps_Component_Plugin_Password_Component')
        ));
        $this->assertEquals(1, count($components));

        $components = $this->_root->getRecursiveChildComponents(array(
            'page' => false,
            'flags' => array('processInput' => true)
        ));
        $this->assertEquals(1, count($components));
    }
}
