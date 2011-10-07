<?php
/**
 * @group slow
 * @group selenium
 * @group Vps_Form_Fieldset
 * Langer Kommentar was das problem ist in Vps_Form_CheckboxFieldsetInCards_Test
 */
class Vps_Form_CheckboxFieldsetInFieldset_Test extends Vps_Test_SeleniumTestCase
{
    public function test()
    {
        $this->open('/vps/test/vps_form_checkbox-fieldset-in-fieldset_test');
        $this->waitForConnections();
        $this->click('//*[text()="Foo"]/preceding::input[@type="checkbox"]');
        $this->click('//*[text()="Bar"]/preceding::input[@type="checkbox"][1]');
        $this->click("//button[text()='".trlVps('Save')."']");
        $this->waitForConnections();
        $this->assertTextPresent(trlVps("Can't save, please fill all red underlined fields correctly."));

        $this->open('/vps/test/vps_form_checkbox-fieldset-in-fieldset_test');
        $this->waitForConnections();
        $this->click('//*[text()="Foo"]/preceding::input[@type="checkbox"]');
        $this->click("//button[text()='".trlVps('Save')."']");
        $this->waitForConnections();
        $this->assertTextNotPresent(trlVps("Can't save, please fill all red underlined fields correctly."));
    }

}
