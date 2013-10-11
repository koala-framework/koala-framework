<?php
/**
 * @group slow
 * @group seleniuim
 * http://kwf.niko.vivid/kwf/kwctest/Kwc_Directories_AjaxView_Root/directory
 */
class Kwc_Directories_AjaxView_SeleniumTest extends Kwf_Test_SeleniumTestCase
{
    public function setUp()
    {
        parent::setUp();
        Kwf_Component_Data_Root::setComponentClass('Kwc_Directories_AjaxView_Root');
    }

    public function testFilterText()
    {
        $this->openKwc('/directory');
        $this->assertElementPresent('link=foo1');
        $this->assertElementPresent('link=foo2');
        $this->type('css=input[type=text]', 'foo1');
        sleep(1);
        $this->waitForConnections();
        $this->assertElementPresent('link=foo1');
        $this->assertElementNotPresent('link=foo2');
    }

    public function testCategory()
    {
        $this->openKwc('/directory');
        $this->click('link=Cat1');
        $this->waitForConnections();
        $this->assertElementPresent('link=foo1');
        $this->assertElementNotPresent('link=foo2');
        $this->click('link=Cat2');
        $this->waitForConnections();
        $this->assertElementNotPresent('link=foo1');
        $this->assertElementNotPresent('link=foo2');
        $this->click('link=directory');
        $this->waitForConnections();
        $this->assertElementPresent('link=foo1');
        $this->assertElementPresent('link=foo2');
    }

    public function testCategoryStartFromCat()
    {
        $this->openKwc('/directory/categories/1_cat1');
        $this->assertElementPresent('link=foo1');
        $this->assertElementNotPresent('link=foo2');
        $this->click('link=Cat2');
        $this->waitForConnections();
        $this->assertElementNotPresent('link=foo1');
        $this->assertElementNotPresent('link=foo2');
        $this->click('link=directory');
        $this->waitForConnections();
        $this->assertElementPresent('link=foo1');
        $this->assertElementPresent('link=foo2');
    }

    public function testDetail()
    {
        $this->openKwc('/directory');
        $this->click('link=foo1');
        $this->waitForConnections();
        $this->assertContainsText('css=.kwcDirectoriesAjaxViewDetail', 'foo1');
        $this->assertNotVisible('link=foo1');
        $this->assertNotVisible('link=foo2');
        $this->click('link=back');
        $this->assertVisible('link=foo1');
        $this->assertVisible('link=foo2');
    }

    public function testFilterReload()
    {
        $this->openKwc('/directory');
        $this->type('css=input[type=text]', 'foo1');
        $this->click('css=button');
        $this->waitForConnections();
        $this->assertElementPresent('link=foo1');
        $this->assertElementNotPresent('link=foo2');
        $this->refreshAndWait(); //must keep filter value
        $this->assertElementPresent('link=foo1');
        $this->assertElementNotPresent('link=foo2');
    }

    public function testHistoryBackFromReloadedDetail()
    {
        $this->openKwc('/directory');
        $this->click('link=foo1');
        $this->waitForConnections();
        $this->refreshAndWait();
        $this->goBackAndWait();
        $this->waitForConnections();
        $this->assertVisible('link=foo2');
    }
}
