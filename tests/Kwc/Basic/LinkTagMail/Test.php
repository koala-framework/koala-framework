<?php
/**
 * @group Vpc_Basic_LinkTagMail
 **/
class Vpc_Basic_LinkTagMail_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vpc_Basic_LinkTagMail_Root');
    }

    public function testUrlAndRel()
    {
        $c = $this->_root->getComponentById(1400);
        $this->assertEquals('mailto:example(vpsat)example(vpsdot)com', $c->url);
        $this->assertEquals('', $c->rel);

        $c = $this->_root->getComponentById(1401);
        $this->assertEquals('mailto:example(vpsat)example(vpsdot)com?subject=dere&body=hallo', $c->url);
        $this->assertEquals('', $c->rel);
    }
    public function testHtml()
    {
        $html = $this->_root->getComponentById(1400)->render();
        $this->assertEquals('<a href="mailto:example(vpsat)example(vpsdot)com">', $html);
    }

    public function testEmpty()
    {
        //ist das das gewünscht verhalten?
        $c = $this->_root->getComponentById(1402);
        $this->assertEquals('', $c->url);
        $this->assertEquals('', $c->rel);
    }
}
