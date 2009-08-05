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
        $this->markTestIncomplete();

        $this->openVpc('/amazon');
        $this->assertContainsText("css=.vpcAdvancedAmazonNodesTestComponent", "Php");
        $this->assertContainsText("css=.vpcAdvancedAmazonNodesTestComponent", "JavaScript");
        $this->clickAndWait('link=Php');
        $this->clickAndWait('link=PHP Design Patterns');
        $this->assertContainsText("css=.vpcAdvancedAmazonNodesProductsDirectoryDetail .bookInfos h1", "PHP Design Patterns");
        $this->assertContainsText("css=.vpcAdvancedAmazonNodesProductsDirectoryDetail .bookInfos h2", "Stephan Schmidt");
        $href = $this->getAttribute('link='.trlVps('order now at amazon').'@href');
        $this->assertEquals('http://www.amazon.de', substr($href, 0, 20));
        $this->assertContains('389721864X', $href);
        $this->assertContains('prosalzburgat-21', $href);
    }
}
