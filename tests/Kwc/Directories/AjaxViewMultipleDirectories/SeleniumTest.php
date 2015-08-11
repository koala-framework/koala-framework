<?php
/**
 * @group slow
 * @group selenium
 * http://kwf.niko.vivid/kwf/kwctest/Kwc_Directories_AjaxViewMultipleDirectories_Root/list
 */
class Kwc_Directories_AjaxViewMultipleDirectories_SeleniumTest extends Kwf_Test_SeleniumTestCase
{
    public function setUp()
    {
        parent::setUp();
        Kwf_Component_Data_Root::setComponentClass('Kwc_Directories_AjaxViewMultipleDirectories_Root');
    }

    public function testDetail()
    {
        $this->openKwc('/list');
        $this->click('link=foo1');
        $this->waitForConnections();
        $this->assertContainsText('css=.kwcDirectoriesAjaxViewMultipleDirectoriesDetail', 'foo1');
        $this->assertNotVisible('link=foo1');
        $this->assertNotVisible('link=foo2');
        $this->click('link=back');
        $this->waitForConnections();
        sleep(2);
        $this->assertVisible('link=foo1');
        $this->assertVisible('link=foo2');
    }

    public function testHistoryBackFromReloadedDetail()
    {
        $this->openKwc('/list');
        $this->click('link=foo1');
        $this->waitForConnections();
        $this->refreshAndWait();
        $this->goBackAndWait();
        $this->waitForConnections();
        sleep(2);
        $this->assertVisible('link=foo2');
    }
}
