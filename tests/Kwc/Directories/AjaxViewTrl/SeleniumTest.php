<?php
/**
 * @group slow
 * @group selenium
 * http://kwf.niko.vivid/kwf/kwctest/Kwc_Directories_AjaxViewTrl_Root_Component/de/directory
 * http://kwf.niko.vivid/kwf/kwctest/Kwc_Directories_AjaxViewTrl_Root_Component/en/directory
 */
class Kwc_Directories_AjaxViewTrl_SeleniumTest extends Kwf_Test_SeleniumTestCase
{
    public function setUp()
    {
        parent::setUp();
        Kwf_Component_Data_Root::setComponentClass('Kwc_Directories_AjaxViewTrl_Root_Component');
    }

    public function testLazyLoadDe()
    {
        $this->openKwc('/de/directory');
        $this->assertElementNotPresent('link=foo30');
        $this->getEval('window.scroll(0, 1000);');
        sleep(1);
        $this->waitForConnections();
        $this->assertElementPresent('link=foo30');
    }

    public function testLazyLoadEn()
    {
        $this->openKwc('/en/directory');
        $this->assertElementNotPresent('link=foo30');
        $this->getEval('window.scroll(0, 1000);');
        sleep(1);
        $this->waitForConnections();
        $this->assertElementPresent('link=foo30');
    }
}
