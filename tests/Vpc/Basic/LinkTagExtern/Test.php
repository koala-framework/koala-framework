<?php
/**
 * @group Vpc_Basic_LinkTagExtern
 **/
class Vpc_Basic_LinkTagExtern_Test extends PHPUnit_Framework_TestCase
{
    private $_root;

    public function setUp()
    {
        Vps_Component_Data_Root::setComponentClass('Vpc_Basic_LinkTagExtern_Root');
        $this->_root = Vps_Component_Data_Root::getInstance();
    }

    public function testUrlAndRel()
    {
        $c = $this->_root->getComponentById(1200);
        $this->assertEquals('http://example.com', $c->url);
        $this->assertEquals('', $c->rel);

        $c = $this->_root->getComponentById(1201);
        $this->assertEquals('http://example.com', $c->url);
        $this->assertEquals('popup_blank', $c->rel);

        $c = $this->_root->getComponentById(1202);
        $this->assertEquals('http://example.com', $c->url);
        $this->assertEquals('popup_width=200,height=300,menubar=yes,toolbar=yes,location=no,status=no,scrollbars=no,resizable=yes', $c->rel);
    }
    public function testHtml()
    {
        $output = new Vps_Component_Renderer();
        $html = $output->render($this->_root->getComponentById(1200));
        $this->assertEquals('<a href="http://example.com">', $html);

        $output = new Vps_Component_Renderer();
        $html = $output->render($this->_root->getComponentById(1201));
        $this->assertEquals('<a href="http://example.com" rel="popup_blank">', $html);
    }
}
