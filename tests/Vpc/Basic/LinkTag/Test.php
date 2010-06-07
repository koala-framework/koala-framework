<?php
/**
 * @group Vpc_Basic_LinkTag
 **/
class Vpc_Basic_LinkTag_Test extends PHPUnit_Framework_TestCase
{
    private $_root;

    public function setUp()
    {
        Vps_Component_Data_Root::setComponentClass('Vpc_Basic_LinkTag_Root');
        $this->_root = Vps_Component_Data_Root::getInstance();
    }

    public function testTemplateVars()
    {
        $c = $this->_root->getComponentById(1100)->getComponent();
        $vars = $c->getTemplateVars();
        $this->assertEquals('1100-link', $vars['linkTag']->componentId);
    }

    public function testUrlAndRel()
    {
        $c = $this->_root->getComponentById(1100);
        $c2 = $c->getChildComponent('-link');
        $this->assertEquals('http://example.com', $c2->url);
        $this->assertEquals('foo', $c2->rel);

        $this->assertEquals('http://example.com', $c->url);
        $this->assertEquals('foo', $c->rel);
    }

    public function testHtml()
    {
        $c = $this->_root->getComponentById(1100);
        $c2 = $c->getChildComponent('-link');

        $output = new Vps_Component_Output_NoCache();
        $html = $output->render($c2);
        $this->assertEquals('<a href="http://example.com" rel="foo">', $html);

        $output = new Vps_Component_Output_NoCache();
        $html = $output->render($c);
        $this->assertEquals('<a href="http://example.com" rel="foo">', $html);
    }
}
