<?php
/**
 * @group Generator_Plugin
 * @author Franz
 */
class Vps_Component_Generator_Plugin_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vps_Component_Generator_Plugin_Root');
    }

    public function testPlugin()
    {
        $components = $this->_root->getChildComponents(array(
            'componentClasses' => array('Vps_Component_Generator_Plugin_Static')
        ));
        $this->assertEquals(1, count($components));

        $components = $this->_root->getRecursiveChildComponents(array(
            'componentClasses' => array('Vps_Component_Generator_Plugin_Static')
        ));
        $this->assertEquals(1, count($components));

        $generators = Vps_Component_Generator_Abstract::getInstances(
            'Vps_Component_Generator_Plugin_Static',
            array('componentClasses' => array('Vps_Component_Plugin_Password_LoginForm_Component'))
        );
        $this->assertEquals(1, count($generators));

        $components = $this->_root->getRecursiveChildComponents(array(
            'componentClasses' => array('Vps_Component_Plugin_Password_LoginForm_Component')
        ));
        $this->assertEquals(1, count($components));

        $components = $this->_root->getRecursiveChildComponents(array(
            'page' => false,
            'flags' => array('processInput' => true)
        ));
        $this->assertEquals(1, count($components));
    }
}
