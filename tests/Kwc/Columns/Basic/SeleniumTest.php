<?php
/**
 * @group slow
 * @group selenium
 * @group Kwc_Columns
 */
class Kwc_Columns_Basic_SeleniumTest extends Kwf_Test_SeleniumTestCase
{
    public function setUp()
    {
        parent::setUp();
        Kwf_Component_Data_Root::setComponentClass('Kwc_Columns_Basic_Root');
    }

    // http://kwf.kwf.niko.vivid/kwf/kwctest/Kwc_Columns_Basic_Root/foo
    // http://kwf.kwf.niko.vivid/kwf/componentedittest/Kwc_Columns_Basic_Root/Kwc_Columns_Basic_TestComponent.Kwc_Columns_Basic_Root/Index?componentId=3000

    public function testAdmin()
    {
        $this->openKwcEdit('Kwc_Columns_Basic_TestComponent.Kwc_Columns_Basic_Root', '3000');
        $this->waitForConnections();
        //test könnte natürlich verbessert werden, aber zumindest testen ob kein fehler kommt
    }
}
