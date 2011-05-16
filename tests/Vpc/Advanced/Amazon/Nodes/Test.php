<?php
/**
 * @group selenium
 * @group slow
 * @group Vpc_Amazon
 */
class Vpc_Advanced_Amazon_Nodes_Test extends Vps_Test_SeleniumTestCase
{
    private $_root;

    public function setUp()
    {
        Vps_Component_Data_Root::setComponentClass('Vpc_Advanced_Amazon_Nodes_Root');
        $this->_root = Vps_Component_Data_Root::getInstance();
        parent::setUp();
    }

    public function testIt()
    {
        $amz = new Vps_Service_Amazon();
        $result = $amz->itemSearch(array('BrowseNode'=>'166039031', 'SearchIndex'=>'Books'));
        $item = $result->current();

        $this->openVpc('/amazon');
        $this->assertContainsText("css=.vpcAdvancedAmazonNodesTestComponent", "Php");
        $this->assertContainsText("css=.vpcAdvancedAmazonNodesTestComponent", "JavaScript");
        $this->clickAndWait('link=Php');

        $t = $item->Title;
        if (mb_strlen($t) > 100) {
            $t = mb_substr($t, 0, 100).'...';
        }

        $this->assertElementPresent('link='.$t);
        $this->clickAndWait('link='.$t);
        $this->assertContainsText("css=.vpcAdvancedAmazonNodesProductsDirectoryDetail .bookInfos h1", $item->Title);
        $this->assertContainsText("css=.vpcAdvancedAmazonNodesProductsDirectoryDetail .bookInfos h2", $item->Author);

        $this->assertElementPresent('link='.trlVps('order now at amazon'));
        $href = $this->getAttribute('link='.trlVps('order now at amazon').'@href');
        $this->assertEquals('http://www.amazon.de', substr($href, 0, 20));
        $this->assertContains($item->ASIN, $href);
        $this->assertContains('prosalzburgat-21', $href);
    }
}
