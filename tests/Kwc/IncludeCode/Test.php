<?php
class Kwc_IncludeCode_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwc_IncludeCode_Root');
    }

    public function testHeaderDirectHtmlOnPage()
    {
        $c = Kwf_Component_Data_Root::getInstance()->getComponentById('root_page1');
        $html = $c->render(null, true);
        $this->assertEquals(substr_count($html, 'content="foo"'), 1);
    }

    public function testHeaderFromChild()
    {
        $c = Kwf_Component_Data_Root::getInstance()->getComponentById('root_page3');
        $html = $c->render(null, true);
        $this->assertEquals(substr_count($html, 'content="foo"'), 1);
    }

    public function testHeaderRenderChild()
    {
        $c = Kwf_Component_Data_Root::getInstance()->getComponentById('root_page2');
        $html = $c->render(null, true);
        $this->assertEquals(substr_count($html, 'content="foo"'), 1);
    }

    public function testFooterDirectHtmlOnPage()
    {
        $c = Kwf_Component_Data_Root::getInstance()->getComponentById('root_page4');
        $html = $c->render(null, true);
        $this->assertEquals(substr_count($html, 'foobar    </body>'), 1);
    }
}
