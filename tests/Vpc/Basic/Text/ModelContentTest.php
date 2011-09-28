<?php
/**
 * Einfachere Tests die nicht mit Unterkomponenten arbeiten.
 * Vorallem Html nach Links/Downloads/Images durchsuchen.
 *
 * @group Vpc_Basic_Text
 **/
class Vpc_Basic_Text_ModelContentTest extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vpc_Basic_Text_Root');
    }

    public function testOnlyText()
    {
        $c = $this->_root->getComponentById(1000)->getComponent();
        $vars = $c->getTemplateVars();
        $this->assertEquals(array('<p>foo</p>'), $vars['contentParts']);
    }

    public function testDefaultValue()
    {
        $c = $this->_root->getComponentById(1001)->getComponent();
        $vars = $c->getTemplateVars();
        $this->assertEquals(1, count($vars['contentParts']));
        $this->assertEquals('<p>Lorem ipsum', substr($vars['contentParts'][0], 0, 14));
    }

    public function testTidyCreatesP()
    {
        $c = $this->_root->getComponentById(1002)->getComponent();
        $row = $c->getRow();
        $html = 'aaa';
        $html = $row->tidy($html);
        $this->assertEquals("<p>\n  aaa\n</p>", $html);
    }

    public function testTidyCleansGarbage()
    {
        $c = $this->_root->getComponentById(1002)->getComponent();
        $row = $c->getRow();
        $html = '<foo>a<span class="asdf">a</span>a</p>';
        $html = $row->tidy($html);
        $this->assertEquals("<p>\n  aaa\n</p>", $html);
    }

    public function testTidyRemovesDoubleStrong()
    {
        $c = $this->_root->getComponentById(1002)->getComponent();
        $row = $c->getRow();
        $html = '<strong>a<strong>b</strong></strong>';
        $html = $row->tidy($html);
        $this->assertEquals("<p>\n  <strong>a</strong>b\n</p>", $html);
    }

    public function testTidyRemovesSomeText()
    {
        $c = $this->_root->getComponentById(1002)->getComponent();
        $row = $c->getRow();
        $html = '<span class=""><span class="fooTest2">xx</span></span>';
        $html = $row->tidy($html);
        $this->assertEquals("<p>\n  xx\n</p>", $html);
    }
    public function testMaxChildComponentNr()
    {
        $c = $this->_root->getComponentById(1000)->getComponent();
        $maxNr = $c->getRow()->getMaxChildComponentNr('link');
        $this->assertEquals(3, $maxNr);

        $c = $this->_root->getComponentById(1000)->getComponent();
        $maxNr = $c->getRow()->getMaxChildComponentNr('download');
        $this->assertEquals(0, $maxNr);
    }

    public function testContentPartsPlainText()
    {
        $c = $this->_root->getComponentById(1003)->getComponent();
        $row = $c->getRow();
        $parts = $row->getContentParts('<p>foo</p>');
        $this->assertEquals(array('<p>foo</p>'), $parts);
    }

    public function testContentPartsInvalidLink()
    {
        $c = $this->_root->getComponentById(1003)->getComponent();
        $row = $c->getRow();
        $parts = $row->getContentParts('<p><a href="http://www.vivid-planet.com/">foo</a></p>');
        $this->assertEquals(array('<p>', array(
                            'type' => 'invalidLink',
                            'href' => 'http://www.vivid-planet.com/',
                            'html' => '<a href="http://www.vivid-planet.com/">',
                        ), 'foo</a></p>'), $parts);

        $parts = $row->getContentParts('<p><a href="mailto:foo@bar.com">foo</a></p>');
        $this->assertEquals(array('<p>', array(
                            'type' => 'invalidLink',
                            'href' => 'mailto:foo@bar.com',
                            'html' => '<a href="mailto:foo@bar.com">',
                        ), 'foo</a></p>'), $parts);
    }

    public function testContentPartsValidLink()
    {
        $c = $this->_root->getComponentById(1003)->getComponent();
        $row = $c->getRow();
        $parts = $row->getContentParts('<p><a href="1003-l1">foo</a></p>');
        $this->assertEquals(array('<p>', array(
                            'type' => 'link',
                            'nr' => '1',
                            'html' => '<a href="1003-l1">',
                        ), 'foo</a></p>'), $parts);

        $parts = $row->getContentParts('<p><a href="http://vivid.com/1003-l1">foo</a></p>');
        $this->assertEquals(array('<p>', array(
                            'type' => 'link',
                            'nr' => '1',
                            'html' => '<a href="http://vivid.com/1003-l1">',
                        ), 'foo</a></p>'), $parts);
    }
    public function testContentPartsInValidLinkComponent()
    {
        $c = $this->_root->getComponentById(1003)->getComponent();
        $row = $c->getRow();
        $parts = $row->getContentParts('<p><a href="1000-l1">foo</a></p>');
        $this->assertEquals(array('<p>', array(
                            'type' => 'invalidLink',
                            'href' => '1000-l1',
                            'componentId' => '1000-l1',
                            'html' => '<a href="1000-l1">',
                        ), 'foo</a></p>'), $parts);
    }

    public function testContentPartsValidDownload()
    {
        $c = $this->_root->getComponentById(1003)->getComponent();
        $row = $c->getRow();
        $parts = $row->getContentParts('<p><a href="1003-d1">foo</a></p>');
        $this->assertEquals(array('<p>', array(
                            'type' => 'download',
                            'nr' => '1',
                            'html' => '<a href="1003-d1">',
                        ), 'foo</a></p>'), $parts);
    }

    public function testContentPartsInValidDownloadComponent()
    {
        $c = $this->_root->getComponentById(1003)->getComponent();
        $row = $c->getRow();
        $parts = $row->getContentParts('<p><a href="1000-d1">foo</a></p>');
        $this->assertEquals(array('<p>', array(
                            'type' => 'invalidDownload',
                            'href' => '1000-d1',
                            'componentId' => '1000-d1',
                            'html' => '<a href="1000-d1">',
                        ), 'foo</a></p>'), $parts);
    }

    public function testContentPartsValidImage()
    {
        $c = $this->_root->getComponentById(1003)->getComponent();
        $row = $c->getRow();
                        //"/media/$class/$id/$rule/$type/$checksum/$filename.$extension$random"
        $parts = $row->getContentParts('<p><img src="/media/Vpc_Basic_Text_TestComponent/1003-i1" />foo</p>');
        $this->assertEquals(array('<p>', array(
                            'type' => 'image',
                            'nr' => '1',
                            'html' => '<img src="/media/Vpc_Basic_Text_TestComponent/1003-i1" />',
                        ), 'foo</p>'), $parts);

        $parts = $row->getContentParts('<p><img src="http://vivid.com/media/Vpc_Basic_Text_TestComponent/1003-i1/default/asdfsadf/test.jpg?garbage" />foo</p>');
        $this->assertEquals(array('<p>', array(
                            'type' => 'image',
                            'nr' => '1',
                            'html' => '<img src="http://vivid.com/media/Vpc_Basic_Text_TestComponent/1003-i1/default/asdfsadf/test.jpg?garbage" />',
                        ), 'foo</p>'), $parts);
    }

    public function testContentPartsInValidImage()
    {
        $c = $this->_root->getComponentById(1003)->getComponent();
        $row = $c->getRow();
        $parts = $row->getContentParts('<p><img src="/foo.jpg" />foo</p>');
        $this->assertEquals(array('<p>', array(
                            'type' => 'invalidImage',
                            'src' => '/foo.jpg',
                            'html' => '<img src="/foo.jpg" />',
                        ), 'foo</p>'), $parts);

        $parts = $row->getContentParts('<p><img src="http://vivid.com/foo.jpg" />foo</p>');
        $this->assertEquals(array('<p>', array(
                            'type' => 'invalidImage',
                            'src' => 'http://vivid.com/foo.jpg',
                            'html' => '<img src="http://vivid.com/foo.jpg" />',
                        ), 'foo</p>'), $parts);
    }

    public function testContentPartsInValidImageComponent()
    {
        $c = $this->_root->getComponentById(1003)->getComponent();
        $row = $c->getRow();
        $parts = $row->getContentParts('<p><img src="/media/Vpc_Basic_Text_TestComponent/1000-i1" />foo</p>');
        $this->assertEquals(array('<p>', array(
                            'type' => 'invalidImage',
                            'src' => '/media/Vpc_Basic_Text_TestComponent/1000-i1',
                            'componentClass' => 'Vpc_Basic_Text_TestComponent',
                            'componentId' => '1000-i1',
                            'html' => '<img src="/media/Vpc_Basic_Text_TestComponent/1000-i1" />',
                        ), 'foo</p>'), $parts);
    }
}
