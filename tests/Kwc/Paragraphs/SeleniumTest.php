<?php
/**
 * @group slow
 * @group selenium
 * @group Kwc_Paragraphs
 */
class Kwc_Paragraphs_SeleniumTest extends Kwf_Test_SeleniumTestCase
{
    public function setUp()
    {
        parent::setUp();
        Kwf_Component_Data_Root::setComponentClass('Kwc_Paragraphs_Paragraphs');
    }

    public function testAdmin()
    {
        $this->openKwcEdit('Kwc_Paragraphs_Paragraphs', 'root');
        $this->waitForConnections();
        //test könnte natürlich verbessert werden, aber zumindest testen ob kein fehler kommt
    }
}
