<?php
/**
 * @group slow
 * @group selenium
 * @group Kwf_Form_Fieldset
 * Langer Kommentar was das problem ist in Kwf_Form_CheckboxFieldsetInCards_Test
 */
class Kwf_Form_CheckboxFieldsetInFieldset_Test extends Kwf_Test_SeleniumTestCase
{
    public function test()
    {
        $this->open('/kwf/test/kwf_form_checkbox-fieldset-in-fieldset_test');
        $this->waitForConnections();
        $this->click('//*[text()="Foo"]/preceding::input[@type="checkbox"]');
        $this->click('//*[text()="Bar"]/preceding::input[@type="checkbox"][1]');
        $this->click("//button[text()='".trlKwf('Save')."']");
        $this->waitForConnections();
        $this->assertTextPresent(trlKwf("Can't save, please fill all red underlined fields correctly."));

        $this->open('/kwf/test/kwf_form_checkbox-fieldset-in-fieldset_test');
        $this->waitForConnections();
        $this->click('//*[text()="Foo"]/preceding::input[@type="checkbox"]');
        $this->click("//button[text()='".trlKwf('Save')."']");
        $this->waitForConnections();
        $this->assertTextNotPresent(trlKwf("Can't save, please fill all red underlined fields correctly."));
    }

}
