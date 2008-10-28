<?php
/**
 * @group Vpc_Basic_LinkTagIntern
 **/
class Vpc_Basic_LinkTagIntern_Test extends PHPUnit_Framework_TestCase
{
    private $_root;

    public function setUp()
    {
        Vps_Component_Data_Root::setComponentClass('Vpc_Basic_LinkTagIntern_Root');
        $this->_root = Vps_Component_Data_Root::getInstance();
    }

    public function testUrlAndRel()
    {
        $c = $this->_root->getComponentById(1300);
        $this->assertEquals('/bar', $c->url);
        $this->assertEquals('', $c->rel);
    }
    public function testHtml()
    {
        $output = new Vps_Component_Output_NoCache();
        $html = $output->render($this->_root->getComponentById(1300));
        $this->assertEquals('<a href="/bar">', $html);
    }

    public function testEmpty()
    {
        //ist das das gewünscht verhalten?
        $c = $this->_root->getComponentById(1301);
        $this->assertEquals('', $c->url);
        $this->assertEquals('', $c->rel);
    }
}
