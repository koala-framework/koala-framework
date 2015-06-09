<?php
/**
 * @group Kwc_Basic_Text
 * @group Kwc_Basic_Text_Model
 */
class Kwc_Basic_TextConvertLinkOnlyExtern_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwc_Basic_TextConvertLinkOnlyExtern_Root');
    }

    public function testCreatesLinkComponent()
    {
        $c = $this->_root->getComponentById(1003)->getComponent();
        $row = $c->getRow();
        $html = '<p><a href="http://www.vivid-planet.com/">foo</a></p>';
        $html = $row->tidy($html);
        $this->assertRegExp("#<p>\n  <a .*?href=\"1003-l1\">foo</a>\n</p>#", $html);

        $cc = array_values($c->getData()->getChildComponents());
        $this->assertEquals(1, count($cc));
        $this->assertEquals('1003-l1', current($cc)->componentId);

        $m = Kwc_Basic_Text_Component::createChildModel($c->getData()->componentClass);
        $rows = $m->getRows($m->select()->whereEquals('component_id', '1003'));
        $this->assertEquals(1, count($rows));
        $row = $rows->current();
        $this->assertEquals('link', $row->component);
        $this->assertEquals('1', $row->nr);

        $m = Kwf_Model_Abstract::getInstance('Kwc_Basic_TextConvertLinkOnlyExtern_LinkExtern_TestModel');
        $rows = $m->getRows($m->select()->whereEquals('component_id', '1003-l1'));
        $this->assertEquals(1, count($rows));
        $row = $rows->current();
        $this->assertEquals('http://www.vivid-planet.com/', $row->target);
    }

    public function testCreatesLinkComponentHtml()
    {
        $c = $this->_root->getComponentById(1004)->getComponent();
        $row = $c->getRow();
        $html = '<p><a href="http://www.vivid-planet.com/">foo</a></p>';
        $row->content = $html;
        $row->save();

        $html = $c->getData()->render();

        $this->assertRegExp("#<div class=\"webStandard kwcText kwcBasicTextConvertLinkOnlyExternTestComponent\">\n".
                    "<p>\n  <a .*?href=\"http://www.vivid-planet.com/\">foo</a>\n</p>".
                    "</div>#", $html);
    }


    public function testCreatesLinkFromOtherComponentId()
    {
        $c = $this->_root->getComponentById(1005)->getComponent();
        $row = $c->getRow();
        $html = '<p><a href="1007-l1">foo</a></p>';
        $row->content = $html;
        $row->save();


        $m = Kwc_Basic_Text_Component::createChildModel($c->getData()->componentClass);
        $rows = $m->getRows($m->select()->whereEquals('component_id', '1005'));
        $this->assertEquals(1, count($rows));
        $row = $rows->current();
        $this->assertEquals('link', $row->component);
        $this->assertEquals('1', $row->nr);

        $m = Kwf_Model_Abstract::getInstance('Kwc_Basic_TextConvertLinkOnlyExtern_LinkExtern_TestModel');
        $rows = $m->getRows($m->select()->whereEquals('component_id', '1005-l1'));
        $this->assertEquals(1, count($rows));
        $row = $rows->current();
        $this->assertEquals('http://vivid.com', $row->target);
    }

    public function testCreatesLinkFromOtherComponentIdHtml()
    {
        $c = $this->_root->getComponentById(1006)->getComponent();
        $row = $c->getRow();
        $html = '<p><a href="1007-l1">foo</a></p>';
        $row->content = $html;
        $row->save();

        $html = $c->getData()->render();
        $this->assertRegExp("#<div class=\"webStandard kwcText kwcBasicTextConvertLinkOnlyExternTestComponent\">\n".
                    "<p>\n  <a .*?href=\"http://vivid.com\">foo</a>\n</p>".
                    "</div>#", $html);
    }
}
