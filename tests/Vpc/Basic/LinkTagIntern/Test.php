<?php
/**
 * @group Vpc_Basic_LinkTagIntern
 **/
class Vpc_Basic_LinkTagIntern_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vpc_Basic_LinkTagIntern_Root');
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
