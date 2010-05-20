<?php
/**
 * @group Vpc_Basic_LinkTag
 **/
class Vpc_Basic_LinkTag_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vpc_Basic_LinkTag_Root');
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

    public function testCacheChangeType()
    {
        $c = $this->_root->getComponentById('1101');

        $this->assertEquals('<a href="http://example2.com" rel="foo">', $c->render());

        $row = $c->getComponent()->getRow();
        $row->component = 'test';
        $row->save();
        $this->_process();

        $c = $this->_root->getComponentById('1101');
        $c = $c->getChildComponent('-link');
        $this->assertEquals('<a href="http://example.com" rel="foo">', $c->render());

        $c = $this->_root->getComponentById('1101');
        $this->assertEquals('<a href="http://example.com" rel="foo">', $c->render());
    }
}
