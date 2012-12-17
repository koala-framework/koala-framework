<?php
/**
 * @group slow
 * @group seleniuim
 * http://kwf.niko.vivid/kwf/kwctest/Kwc_Directories_AjaxViewTwoOnOnePage_Root/directory
 * http://kwf.niko.vivid/kwf/kwctest/Kwc_Directories_AjaxViewTwoOnOnePage_Root/test
 */
class Kwc_Directories_AjaxViewTwoOnOnePage_SeleniumTest extends Kwf_Test_SeleniumTestCase
{
    public function setUp()
    {
        parent::setUp();
        Kwf_Component_Data_Root::setComponentClass('Kwc_Directories_AjaxViewTwoOnOnePage_Root');
    }

    public function testDetail()
    {
        $this->openKwc('/directory');
        $this->click('link=foo1');
        $this->waitForConnections();
        $this->assertContainsText('css=.kwcDirectoriesAjaxViewTwoOnOnePageDetail', 'foo1');
        $this->assertNotVisible('link=foo1');
        $this->assertNotVisible('link=foo2');
        $this->click('link=back');
        $this->assertVisible('link=foo1');
        $this->assertVisible('link=foo2');
    }
}
