<?php
/**
 * @group Vpc_Basic_LinkTagMail
 **/
class Vpc_Basic_LinkTagMail_Test extends PHPUnit_Framework_TestCase
{
    private $_root;

    public function setUp()
    {
        Vps_Component_Data_Root::setComponentClass('Vpc_Basic_LinkTagMail_Root');
        $this->_root = Vps_Component_Data_Root::getInstance();
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
        $output = new Vps_Component_Renderer();
        $html = $output->render($this->_root->getComponentById(1400));
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
