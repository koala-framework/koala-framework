<?php
/**
 * @group Generator_Plugin
 * @author Franz
 */
class Kwf_Component_Generator_Plugin_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwf_Component_Generator_Plugin_Root');
    }

    public function testPlugin()
    {
        $components = $this->_root->getChildComponents(array(
            'componentClasses' => array('Kwf_Component_Generator_Plugin_Static')
        ));
        $this->assertEquals(1, count($components));

        $components = $this->_root->getRecursiveChildComponents(array(
            'componentClasses' => array('Kwf_Component_Generator_Plugin_Static')
        ));
        $this->assertEquals(1, count($components));

        $generators = Kwf_Component_Generator_Abstract::getInstances(
            'Kwf_Component_Generator_Plugin_Static',
            array('componentClasses' => array('Kwf_Component_Plugin_Password_LoginForm_Component'))
        );
        $this->assertEquals(1, count($generators));

        $components = $this->_root->getRecursiveChildComponents(array(
            'componentClasses' => array('Kwf_Component_Plugin_Password_LoginForm_Component')
        ));
        $this->assertEquals(1, count($components));

        $components = $this->_root->getRecursiveChildComponents(array(
            'page' => false,
            'flags' => array('processInput' => true)
        ));
        $this->assertEquals(1, count($components));
    }
}
