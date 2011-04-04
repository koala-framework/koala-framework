<?php
/**
 * @group Vpc_Basic_LinkTagExtern
 **/
class Vpc_Basic_LinkTagExtern_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vpc_Basic_LinkTagExtern_Root');
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
        $html = $this->_root->getComponentById(1200)->render();
        $this->assertEquals('<a href="http://example.com">', $html);

        $html = $this->_root->getComponentById(1201)->render();
        $this->assertEquals('<a href="http://example.com" rel="popup_blank">', $html);
    }
}
