<?php
/**
 * @group selenium
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
        $this->clickAndWait('link=PHP 5 / MySQL 5. Studienausgabe');
    }
}
