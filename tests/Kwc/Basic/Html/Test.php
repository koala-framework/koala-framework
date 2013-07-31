<?php
/**
 * @group Kwc_Basic_Html
 **/
class Kwc_Basic_Html_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwc_Basic_Html_Root');
    }

    public function testTemplateVars()
    {
        $renderer = new Kwf_Component_Renderer();

        $c = $this->_root->getComponentById(2000)->getComponent();
        $vars = $c->getTemplateVars($renderer);
        $this->assertEquals('<p>foo</p>', $vars['content']);

        $c = $this->_root->getComponentById(2001)->getComponent();
        $vars = $c->getTemplateVars($renderer);
        $this->assertNotEquals('<p>foo{test}bar</p>', $vars['content']);

        $c = $this->_root->getComponentById(2002)->getComponent();
        $vars = $c->getTemplateVars($renderer);
        $this->assertEquals('<p>foo{testx}bar</p>', $vars['content']);

        $c = $this->_root->getComponentById(2003)->getComponent();
        $vars = $c->getTemplateVars($renderer);
        $this->assertEquals('<p>foo{testbar</p>', $vars['content']);
    }

    public function testOutput()
    {
        $c = $this->_root->getComponentById(2001)->getComponent();
        $html = $c->getData()->render();
        $this->assertEquals("<div class=\"kwcBasicHtmlTestComponent\">\n    <p>foochildbar</p></div>",
                            $html);
    }

    public function testDefaultValue()
    {
        $renderer = new Kwf_Component_Renderer();

        $m = Kwf_Model_Abstract::getInstance('Kwc_Basic_Html_TestModel');
        $r = $m->createRow();
        $this->assertEquals('ShouldGetOverwritten', $r->content);

        $c = $this->_root->getComponentById(2004)->getComponent();
        $vars = $c->getTemplateVars($renderer);
        $this->assertEquals('ShouldGetOverwritten', $vars['content']);
    }
}
