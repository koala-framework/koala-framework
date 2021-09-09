<?php
/**
 * @group Kwc_Basic_LinkTagExtern
 **/
class Kwc_Basic_LinkTagExtern_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwc_Basic_LinkTagExtern_Root');
    }

    public function testUrlAndRel()
    {
        $c = $this->_root->getComponentById(1200);
        $this->assertEquals('http://example.com', $c->url);
        $this->assertEquals('', $c->rel);

        $c = $this->_root->getComponentById(1201);
        $this->assertEquals('http://example.com', $c->url);
        $this->assertEquals(array('kwc-popup'=>'blank'), $c->getLinkDataAttributes());

        $c = $this->_root->getComponentById(1202);
        $this->assertEquals('http://example.com', $c->url);
        $this->assertEquals('', $c->rel);
        $attrs = $c->getLinkDataAttributes();
        $this->assertEquals('width=200,height=300', $attrs['kwc-popup']);
    }

    public function testHtml()
    {
        $html = $this->_root->getComponentById(1200)->render();
        $this->assertRegExp('#<a .*?href="http://example.com">#', $html);

        $html = $this->_root->getComponentById(1201)->render();
        $this->assertRegExp('#<a .*?href="http://example.com" data-kwc-popup="blank">#', $html);
    }
}
