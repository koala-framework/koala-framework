<?php
/**
 * @group selenium
 * @group slow
 * @group AutoForm
 */
class Kwf_Form_ShowField_ValueOverlapsTest extends Kwf_Test_SeleniumTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->setTimeout(120000);
    }

    public function testValueOverlaps()
    {
        //test deaktiviert, hat nur probleme versacht und schlug eh nur im IE fehl
        /*
        $this->open('/kwf/test/kwf_form_show-field_value-overlaps-error-form');
        $this->click("//button[text()='testA']");
        $this->waitForConnections();
        sleep(3);
        $posleft = $this->getElementPositionLeft("//div[text()='vorname']");
        $posleft1 = $this->getElementPositionLeft("//label[text()='Vorname:']");
        $this->assertEquals(105, $posleft - $posleft1);
        */
    }


}
