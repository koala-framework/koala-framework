<?php
/**
 * @group Vpc_Basic_LinkTagFirstChildPage
 **/
class Vpc_Basic_LinkTagFirstChildPage_Test extends PHPUnit_Framework_TestCase
{
    private $_root;

    public function setUp()
    {
        Vps_Component_Data_Root::setComponentClass('Vpc_Basic_LinkTagFirstChildPage_Root');
        $this->_root = Vps_Component_Data_Root::getInstance();
    }

    public function testUrlAndRel()
    {
        $c = $this->_root->getComponentById(1500);
        $this->assertEquals('/foo1/bar1', $c->url);
        $this->assertEquals('', $c->rel);

        $c = $this->_root->getComponentById(1502);
        $this->assertEquals('/foo2/bar2/baz2', $c->url);
        $this->assertEquals('', $c->rel);

    }
    public function testHtml()
    {
        $html = $this->_root->getComponentById(1500)->render();
        $this->assertEquals('<a href="/foo1/bar1">', $html);
    }

    public function testEmpty()
    {
        //ist das das gewünscht verhalten?
        $c = $this->_root->getComponentById(1505);
        $this->assertEquals('', $c->url);
        $this->assertEquals('', $c->rel);
    }
}
