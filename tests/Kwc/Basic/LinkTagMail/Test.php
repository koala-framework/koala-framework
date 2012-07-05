<?php
/**
 * @group Kwc_Basic_LinkTagMail
 **/
class Kwc_Basic_LinkTagMail_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwc_Basic_LinkTagMail_Root');
    }

    public function testUrlAndRel()
    {
        $c = $this->_root->getComponentById(1400);
        $this->assertEquals('mailto:example(kwfat)example(kwfdot)com', $c->url);
        $this->assertEquals('', $c->rel);

        $c = $this->_root->getComponentById(1401);
        $this->assertEquals('mailto:example(kwfat)example(kwfdot)com?subject=dere&body=hallo', $c->url);
        $this->assertEquals('', $c->rel);
    }
    public function testHtml()
    {
        $html = $this->_root->getComponentById(1400)->render();
        $this->assertRegExp('#<a .*?href="mailto:example\(kwfat\)example\(kwfdot\)com">#', $html);
    }

    public function testEmpty()
    {
        //ist das das gewünscht verhalten?
        $c = $this->_root->getComponentById(1402);
        $this->assertEquals('', $c->url);
        $this->assertEquals('', $c->rel);
    }
}
