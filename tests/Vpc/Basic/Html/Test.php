<?php
/**
 * @group Vpc_Basic_Html
 **/
class Vpc_Basic_Html_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vpc_Basic_Html_Root');
    }

    public function testTemplateVars()
    {
        $c = $this->_root->getComponentById(2000)->getComponent();
        $vars = $c->getTemplateVars();
        $this->assertEquals('<p>foo</p>', $vars['content']);

        $c = $this->_root->getComponentById(2001)->getComponent();
        $vars = $c->getTemplateVars();
        $this->assertNotEquals('<p>foo{test}bar</p>', $vars['content']);

        $c = $this->_root->getComponentById(2002)->getComponent();
        $vars = $c->getTemplateVars();
        $this->assertEquals('<p>foo{testx}bar</p>', $vars['content']);

        $c = $this->_root->getComponentById(2003)->getComponent();
        $vars = $c->getTemplateVars();
        $this->assertEquals('<p>foo{testbar</p>', $vars['content']);
    }

    public function testOutput()
    {
        $c = $this->_root->getComponentById(2001)->getComponent();
        $html = $c->getData()->render();
        $this->assertEquals("<div class=\"vpcBasicHtmlTestComponent\">\n    <p>foochildbar</p></div>",
                            $html);
    }

    public function testDefaultValue()
    {
        $m = Vps_Model_Abstract::getInstance('Vpc_Basic_Html_TestModel');
        $r = $m->createRow();
        $this->assertEquals('ShouldGetOverwritten', $r->content);

        $c = $this->_root->getComponentById(2004)->getComponent();
        $vars = $c->getTemplateVars();
        $this->assertEquals('ShouldGetOverwritten', $vars['content']);
    }
}
