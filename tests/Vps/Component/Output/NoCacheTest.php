<?php
/**
 * @group Component_Output
 */
class Vps_Component_Output_NoCacheTest extends PHPUnit_Framework_TestCase
{
    protected $_output;
    
    public function setUp()
    {
        Vps_Component_Data_Root::setComponentClass('Vps_Component_Output_Root');
        $this->_root = Vps_Component_Data_Root::getInstance();
        $this->_output = new Vps_Component_Output_NoCache();
    }

    public function testComponentOutput()
    {
        $output = $this->_output->render($this->_root, dirname(__FILE__) . '/../Master.tpl');
        $this->assertEquals('master box root plugin(plugin(child child2))', $output);
        
        $output = $this->_output->render($this->_root);
        $this->assertEquals('root plugin(plugin(child child2))', $output);
        
        $output = $this->_output->render($this->_root->getChildComponent('-child'));
        $this->assertEquals('child child2', $output);
    }
}