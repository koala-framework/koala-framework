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
        $amz = new Kwf_Service_Amazon();
        $result = $amz->itemSearch(array('BrowseNode'=>'166039031', 'SearchIndex'=>'Books'));
        $item = $result->current();

        $this->openKwc('/amazon');
        $this->assertContainsText("css=.kwcAdvancedAmazonNodesTestComponent", "Php");
        $this->assertContainsText("css=.kwcAdvancedAmazonNodesTestComponent", "JavaScript");
        $this->clickAndWait('link=Php');

        $t = $item->Title;
        if (mb_strlen($t) > 100) {
            $t = mb_substr($t, 0, 100).'...';
        }

        $this->assertElementPresent('link='.$t);
        $this->clickAndWait('link='.$t);
        $this->assertContainsText("css=.kwcAdvancedAmazonNodesProductsDirectoryDetail .bookInfos h1", $item->Title);
        $this->assertContainsText("css=.kwcAdvancedAmazonNodesProductsDirectoryDetail .bookInfos h2", $item->Author);

        $this->assertElementPresent('link='.trlKwf('order now at amazon'));
        $href = $this->getAttribute('link='.trlKwf('order now at amazon').'@href');
        $this->assertEquals('http://www.amazon.de', substr($href, 0, 20));
        $this->assertContains($item->ASIN, $href);
        $this->assertContains('prosalzburgat-21', $href);
    }
}
