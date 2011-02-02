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
        $this->markTestIncomplete(); // TODO: deaktiviert, gehoert mal verbessert damit es nicht immer veraltet
        $this->assertElementPresent('link=PHP 5.3 und MySQL 5.5: Grundlagen, Anwendung, Praxiswissen, Objektorientierung, MVC, Sichere Weba...');
        $this->clickAndWait('link=PHP 5.3 und MySQL 5.5: Grundlagen, Anwendung, Praxiswissen, Objektorientierung, MVC, Sichere Weba...');
        $this->assertContainsText("css=.vpcAdvancedAmazonNodesProductsDirectoryDetail .bookInfos h1", "PHP 5.3 und MySQL 5.5: Grundlagen, Anwendung");
        $this->assertContainsText("css=.vpcAdvancedAmazonNodesProductsDirectoryDetail .bookInfos h2", "Stefan Reimers, Gunnar Thies");
        $this->assertElementPresent('link='.trlVps('order now at amazon'));
        $href = $this->getAttribute('link='.trlVps('order now at amazon').'@href');
        $this->assertEquals('http://www.amazon.de', substr($href, 0, 20));
        $this->assertContains('3836216450', $href);
        $this->assertContains('prosalzburgat-21', $href);
    }
}
