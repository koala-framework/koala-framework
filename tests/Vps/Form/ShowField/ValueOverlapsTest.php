<?php
/**
 * @group selenium
 * @group AutoForm
 */
class Vps_Form_ShowField_ValueOverlapsTest extends Vps_Test_SeleniumTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->setTimeout(120000);
    }

    public function testValueOverlaps()
    {
        $this->open('/vps/test/vps_form_show-field_value-overlaps-error');
        $this->click("//button[text()='testA']");
        $this->waitForConnections();
        $posleft = $this->getElementPositionLeft("//div[text()='vorname']");
        $posleft1 = $this->getElementPositionLeft("//label[text()='Vorname:']");
        $this->assertEquals(105, $posleft - $posleft1);
    }


}
