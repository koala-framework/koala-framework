<?php
/**
 * @group Vpc_Basic_Text
 * @group Vpc_Basic_Text_Model
 **/
class Vpc_Basic_Text_ModelTest extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vpc_Basic_Text_Root');
    }

    public function testCreatesLinkComponent()
    {
        $c = $this->_root->getComponentById(1003)->getComponent();
        $row = $c->getRow();
        $html = '<p><a href="http://www.vivid-planet.com/">foo</a></p>';
        $html = $row->tidy($html);
        $this->assertEquals("<p>\n  <a href=\"1003-l1\">foo</a>\n</p>", $html);

        $cc = array_values($c->getData()->getChildComponents());
        $this->assertEquals(1, count($cc));
        $this->assertEquals('1003-l1', current($cc)->componentId);

        $m = Vpc_Basic_Text_Component::getTextModel($c->getData()->componentClass)
            ->getDependentModel('ChildComponents');
        $rows = $m->getRows($m->select()->whereEquals('component_id', '1003'));
        $this->assertEquals(1, count($rows));
        $row = $rows->current();
        $this->assertEquals('link', $row->component);
        $this->assertEquals('1', $row->nr);

        $m = Vps_Model_Abstract::getInstance('Vpc_Basic_Text_Link_TestModel');
        $rows = $m->getRows($m->select()->whereEquals('component_id', '1003-l1'));
        $this->assertEquals(1, count($rows));
        $row = $rows->current();
        $this->assertEquals('extern', $row->component);

        $m = Vps_Model_Abstract::getInstance('Vpc_Basic_Text_Link_Extern_TestModel');
        $rows = $m->getRows($m->select()->whereEquals('component_id', '1003-l1-link'));
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

        $output = new Vps_Component_Output_NoCache();
        $html = $output->render($c->getData());

        $this->assertEquals("<div class=\"webStandard vpcText vpcBasicTextTestComponent\">\n".
                    "<p>\n  <a href=\"http://www.vivid-planet.com/\">foo</a>\n</p>".
                    "</div>", $html);

    }

    public function testCreatesInternLinkComponent()
    {
        $c = $this->_root->getComponentById(1014)->getComponent();
        $row = $c->getRow();
        $html = '<p><a href="/foo1">foo</a></p>';
        $row->content = $html;
        $row->save();
        $html = $row->content;
        $this->assertEquals("<p>\n  <a href=\"1014-l1\">foo</a>\n</p>", $html);

        $cc = array_values($c->getData()->getChildComponents());
        $this->assertEquals(1, count($cc));
        $this->assertEquals('1014-l1', current($cc)->componentId);

        $m = Vpc_Basic_Text_Component::getTextModel($c->getData()->componentClass)
            ->getDependentModel('ChildComponents');
        $rows = $m->getRows($m->select()->whereEquals('component_id', '1014'));
        $this->assertEquals(1, count($rows));
        $row = $rows->current();
        $this->assertEquals('link', $row->component);
        $this->assertEquals('1', $row->nr);

        $m = Vps_Model_Abstract::getInstance('Vpc_Basic_Text_Link_TestModel');
        $rows = $m->getRows($m->select()->whereEquals('component_id', '1014-l1'));
        $this->assertEquals(1, count($rows));
        $row = $rows->current();
        $this->assertEquals('intern', $row->component);

        $m = Vps_Model_Abstract::getInstance('Vpc_Basic_Text_Link_Intern_TestModel');
        $rows = $m->getRows($m->select()->whereEquals('component_id', '1014-l1-link'));
        $this->assertEquals(1, count($rows));
        $row = $rows->current();
        $this->assertEquals('1001', $row->target);

        $output = new Vps_Component_Output_NoCache();
        $html = $output->render($c->getData());
        $this->assertEquals("<div class=\"webStandard vpcText vpcBasicTextTestComponent\">\n".
                    "<p>\n  <a href=\"/vps/vpctest/Vpc_Basic_Text_Root/foo1\">foo</a>\n</p>".
                    "</div>", $html);
    }

    public function testCreatesMailLinkComponentHtml()
    {
        $c = $this->_root->getComponentById(1005)->getComponent();
        $row = $c->getRow();
        $html = '<p><a href="mailto:foo@example.com">foo</a></p>';
        $row->content = $html;
        $row->save();

        $output = new Vps_Component_Output_NoCache();
        $html = $output->render($c->getData());
        $this->assertEquals("<div class=\"webStandard vpcText vpcBasicTextTestComponent\">\n".
                    "<p>\n  <a href=\"mailto:foo(vpsat)example(vpsdot)com\">foo</a>\n</p>".
                    "</div>", $html);
    }

    public function testCreatesLinkFromOtherComponentId()
    {
        $c = $this->_root->getComponentById(1006)->getComponent();
        $row = $c->getRow();
        $html = '<p><a href="1007-l1">foo</a></p>';
        $row->content = $html;
        $row->save();

        $output = new Vps_Component_Output_NoCache();
        $html = $output->render($c->getData());
        $this->assertEquals("<div class=\"webStandard vpcText vpcBasicTextTestComponent\">\n".
                    "<p>\n  <a href=\"http://vivid.com\">foo</a>\n</p>".
                    "</div>", $html);
    }

    public function testCreatesImageComponentx()
    {
        $c = $this->_root->getComponentById(1008)->getComponent();
        $row = $c->getRow();
        $html = '<p><img src="http://www.vivid-planet.com/assets/web/images/structure/logo.png" /></p>';
        $html = $row->tidy($html);
        $this->assertRegExp("#^<p>\n  <img src=\"/vps/vpctest/Vpc_Basic_Text_Root/media/Vpc_Basic_Text_Image_TestComponent/1008-i1/default/[0-9a-z]+/[0-9]+/logo.png\" width=\"100\" height=\"100\" />\n</p>$#ms", $html);

        $cc = array_values($c->getData()->getChildComponents());
        $this->assertEquals(1, count($cc));
        $this->assertEquals('1008-i1', current($cc)->componentId);

        $m = Vpc_Basic_Text_Component::getTextModel($c->getData()->componentClass)
            ->getDependentModel('ChildComponents');
        $rows = $m->getRows($m->select()->whereEquals('component_id', '1008'));
        $this->assertEquals(1, count($rows));
        $row = $rows->current();
        $this->assertEquals('image', $row->component);
        $this->assertEquals('1', $row->nr);

        $m = Vps_Model_Abstract::getInstance('Vpc_Basic_Text_Image_TestModel');
        $rows = $m->getRows($m->select()->whereEquals('component_id', '1008-i1'));
        $this->assertEquals(1, count($rows));
        $row = $rows->current();
        $this->assertEquals(2, $row->vps_upload_id);

        $m = Vps_Model_Abstract::getInstance('Vpc_Basic_Text_Image_UploadsModel');
        $row = $m->getRow(2);
        $this->assertEquals('image/png', $row->mime_type);
        $this->assertEquals('png', $row->extension);
        $this->assertEquals('logo', $row->filename);
        $this->assertEquals(file_get_contents($m->getUploadDir().'/2'),
                            file_get_contents('http://www.vivid-planet.com/assets/web/images/structure/logo.png'));
    }

    public function testCreatesImageComponentHtml()
    {
        $c = $this->_root->getComponentById(1009)->getComponent();
        $row = $c->getRow();
        $html = '<p><img src="http://www.vivid-planet.com/assets/web/images/structure/logo.png" /></p>';
        $row->content = $html;
        $row->save();

        $output = new Vps_Component_Output_NoCache();
        $html = $output->render($c->getData());

        $this->assertRegExp('#^\s*<div class="webStandard vpcText vpcBasicTextTestComponent">'.
                    '\s*<p>\s*<div class="vpcBasicTextImageTestComponent">'
                    .'\s*<img src="/vps/vpctest/Vpc_Basic_Text_Root/media/Vpc_Basic_Text_Image_TestComponent/1009-i1/default/[0-9a-z]+/[0-9]+/logo.png" width="100" height="100" alt="" class="" />'
                    .'\s*</div>\s*</p>'
                    .'\s*</div>\s*$#ms', $html);

    }

    public function testCreatesImageFromOtherComponentId()
    {
        $c = $this->_root->getComponentById(1010)->getComponent();
        $row = $c->getRow();
        $html = '<p><img src="/media/Vpc_Basic_Text_Image_TestComponent/1011-i1/default/asdf/blub.png" /></p>';
        $row->content = $html;
        $row->save();

        $output = new Vps_Component_Output_NoCache();
        $html = $output->render($c->getData());
        $this->assertRegExp('#^\s*<div class="webStandard vpcText vpcBasicTextTestComponent">'
                    .'\s*<p>\s*<div class="vpcBasicTextImageTestComponent">'
                    .'\s*<img src="/vps/vpctest/Vpc_Basic_Text_Root/media/Vpc_Basic_Text_Image_TestComponent/1010-i1/default/9ab09415a09caef30d520e9080262b7f/[0-9]+/foo.png" width="100" height="100" alt="" class="" />'
                    .'\s*</div>\s*</p>'
                    .'\s*</div>\s*$#ms', $html);
    }

    public function testCreatesDownloadFromOtherComponentId()
    {
        $c = $this->_root->getComponentById(1012)->getComponent();
        $row = $c->getRow();
        $html = '<p><a href="1013-d1">foo</a></p>';
        $row->content = $html;
        $row->save();

        $output = new Vps_Component_Output_NoCache();
        $html = $output->render($c->getData());
        $this->assertRegExp("#^<div class=\"webStandard vpcText vpcBasicTextTestComponent\">\n".
                    "<p>\n  <a href=\"/vps/vpctest/Vpc_Basic_Text_Root/media/Vpc_Basic_Text_Download_TestComponent/1012-d1/default/a1b024ef219bcfe6b3f5ac9916d8f722/[0-9]+/foo.png\" rel=\"popup_blank\">foo</a>\n</p>".
                    "</div>$#ms", $html);
    }

    public function testOldMediaUrlImage()
    {
        $c = $this->_root->getComponentById(1015)->getComponent();
        $row = $c->getRow();
        $html = '<p><img src="/media/Vpc_Basic_Text_Image_TestComponent/1015-i1/File/small/e73520d11dee6ff49859b8bb26fc631f/filename.jpg?319" /></p>';
        $row->content = $html;
        $row->save();
        $html = $row->content;
        $this->assertEquals("<p>\n  <img src=\n  \"/media/Vpc_Basic_Text_Image_TestComponent/1015-i1/File/small/e73520d11dee6ff49859b8bb26fc631f/filename.jpg?319\" />\n</p>", $html);

        $output = new Vps_Component_Output_NoCache();
        $html = $output->render($c->getData());
        $this->assertRegExp('#^\s*<div class="webStandard vpcText vpcBasicTextTestComponent">'
                    .'\s*<p>\s*<div class="vpcBasicTextImageTestComponent">'
                    .'\s*<img src="/vps/vpctest/Vpc_Basic_Text_Root/media/Vpc_Basic_Text_Image_TestComponent/1015-i1/default/987577de8b2c5b4b75b8343ed85db0bf/[0-9]+/foo.png" width="100" height="100" alt="" class="" />'
                    .'\s*</div>\s*</p>'
                    .'\s*</div>\s*$#ms', $html);
    }
}
