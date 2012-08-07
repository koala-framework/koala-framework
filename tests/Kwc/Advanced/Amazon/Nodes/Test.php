<?php
/**
 * @group selenium
 * @group slow
 * @group Kwc_Amazon
 */
class Kwc_Advanced_Amazon_Nodes_Test extends Kwf_Test_SeleniumTestCase
{
    private $_root;

    public function setUp()
    {
        Kwf_Component_Data_Root::setComponentClass('Kwc_Advanced_Amazon_Nodes_Root');
        $this->_root = Kwf_Component_Data_Root::getInstance();
        parent::setUp();
    }

    public function testIt()
    {
        $this->openKwc('/amazon');
        $this->assertContainsText("css=.kwcAdvancedAmazonNodesTestComponent", "Php");
        $this->assertContainsText("css=.kwcAdvancedAmazonNodesTestComponent", "JavaScript");
        $this->clickAndWait('link=Php');

        $this->assertElementPresent('css=li.products a');
        $this->clickAndWait('css=li.products a');
        $this->assertElementPresent('link='.trlKwf('order now at amazon'));
        $href = $this->getAttribute('link='.trlKwf('order now at amazon').'@href');
        $this->assertEquals('http://www.amazon.de', substr($href, 0, 20));
        $this->assertContains('vps-21', $href);
    }
}
