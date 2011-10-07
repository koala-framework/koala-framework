<?php
/**
 * @group slow
 * @group selenium
 * @group Vpc_Paragraphs
 */
class Vpc_Paragraphs_SeleniumTest extends Vps_Test_SeleniumTestCase
{
    public function setUp()
    {
        parent::setUp();
        Vps_Component_Data_Root::setComponentClass('Vpc_Paragraphs_Paragraphs');
    }

    public function testAdmin()
    {
        $this->openVpcEdit('Vpc_Paragraphs_Paragraphs', 'root');
        $this->waitForConnections();
        //test könnte natürlich verbessert werden, aber zumindest testen ob kein fehler kommt
    }
}
