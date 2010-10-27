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
        $this->openVpc('/amazon');
        $this->assertContainsText("css=.vpcAdvancedAmazonNodesTestComponent", "Php");
        $this->assertContainsText("css=.vpcAdvancedAmazonNodesTestComponent", "JavaScript");
        $this->clickAndWait('link=Php');
        $this->assertElementPresent('link=Das Zend Framework: Von den Grundlagen bis zur fertigen Anwendung');
        $this->clickAndWait('link=Das Zend Framework: Von den Grundlagen bis zur fertigen Anwendung');
        $this->assertContainsText("css=.vpcAdvancedAmazonNodesProductsDirectoryDetail .bookInfos h1", "Das Zend Framework");
        $this->assertContainsText("css=.vpcAdvancedAmazonNodesProductsDirectoryDetail .bookInfos h2", "Ralf Eggert");
        $this->assertElementPresent('link='.trlVps('order now at amazon'));
        $href = $this->getAttribute('link='.trlVps('order now at amazon').'@href');
        $this->assertEquals('http://www.amazon.de', substr($href, 0, 20));
        $this->assertContains('3827327857', $href);
        $this->assertContains('prosalzburgat-21', $href);
    }
}
