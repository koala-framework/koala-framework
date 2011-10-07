<?php
// dient zB auch dem Kwc_Basic_LinkTagNews test
/**
 * @group Kwc_News
 */
class Kwc_News_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwc_News_Root');
        $this->_root->setFilename(null);
    }

    public function testBasic()
    {
        $newsDir = $this->_root->getComponentById(2100);
        $this->assertEquals('/newsbar1', $newsDir->url);

        $newsDetail = $newsDir->getChildComponent('_501');
        $this->assertEquals('/newsbar1/501_a', $newsDetail->url);
    }
}
