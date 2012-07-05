<?php
/**
 * @group Kwc_Basic_LinkTagParentPage
 **/
class Kwc_Basic_LinkTagParentPage_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwc_Basic_LinkTagParentPage_Root');
        $this->_root->setFilename(null);
    }

    public function testUrlAndRel()
    {
        $c = $this->_root->getComponentById(1402); // linkt auf 1400
        $this->assertEquals('/foo1', $c->url);
        $this->assertEquals('', $c->rel);

        $c = $this->_root->getComponentById(1401); // ist hauptseite und kann nicht nach oben linken
        $this->assertEquals('', $c->url);
        $this->assertEquals('', $c->rel);
    }

    public function testHtml()
    {
        $html = $this->_root->getComponentById(1402)->render();
        $this->assertRegExp('#<a .*?href="/foo1">#', $html);
    }
}
