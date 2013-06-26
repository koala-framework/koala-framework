<?php
class Kwc_List_GalleryBasic_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwc_List_GalleryBasic_Root');
        $this->_root->setFilename('');
    }

    public function testInitial()
    {
        $html = $this->_root->getComponentById('root_page1-1-linkTag_imagePage')->render();
        $this->assertNotContains('prevBtn">', $html);
        $this->assertRegexp('#nextBtn">\s*<a href="/page1/2:image"#s', $html);

        $html = $this->_root->getComponentById('root_page1-2-linkTag_imagePage')->render();
        $this->assertRegexp('#prevBtn">\s*<a href="/page1/1:image"#s', $html);
        $this->assertRegexp('#nextBtn">\s*<a href="/page1/3:image"#s', $html);

        $html = $this->_root->getComponentById('root_page1-3-linkTag_imagePage')->render();
        $this->assertRegexp('#prevBtn">\s*<a href="/page1/2:image"#s', $html);
        $this->assertNotContains('nextBtn">', $html);
    }

    public function testChangeOrder()
    {
        $this->_root->getComponentById('root_page1-1-linkTag_imagePage')->render();
        $this->_root->getComponentById('root_page1-2-linkTag_imagePage')->render();
        $this->_root->getComponentById('root_page1-3-linkTag_imagePage')->render();

        $m = Kwf_Model_Abstract::getInstance('Kwc_List_GalleryBasic_TestModel');
        $row = $m->getRow(2);
        $row->pos = 1;
        $row->save();
        $this->_process();

        $html = $this->_root->getComponentById('root_page1-2-linkTag_imagePage')->render();
        $this->assertNotContains('prevBtn">', $html);
        $this->assertRegexp('#nextBtn">\s*<a href="/page1/1:image"#s', $html);

        $html = $this->_root->getComponentById('root_page1-1-linkTag_imagePage')->render();
        $this->assertRegexp('#prevBtn">\s*<a href="/page1/2:image"#s', $html);
        $this->assertRegexp('#nextBtn">\s*<a href="/page1/3:image"#s', $html);

        $html = $this->_root->getComponentById('root_page1-3-linkTag_imagePage')->render();
        $this->assertRegexp('#prevBtn">\s*<a href="/page1/1:image"#s', $html);
        $this->assertNotContains('nextBtn">', $html);
    }
}
