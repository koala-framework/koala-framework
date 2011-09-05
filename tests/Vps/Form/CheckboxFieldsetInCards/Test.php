<?php
/**
 * @group slow
 * @group selenium
 * @group Vps_Form_FieldsetInCards
 *
 * Testen den Bug der auftritt wenn in einem Cards ein fieldset ist das per checkbox
 * deaktiviert werden kann.
 * Wenn auf eine Card umgeschalten wird, werden rekursiv alle Felder in dieser Card
 * enabled, was dann auch die felder in dem Fieldset aktiviert - obwohl es möglicherweise
 * nicht angehakt ist.
 * Dadurch wird dann im deaktivierten fieldset validiert.
 *
 * genau das gleiche mit fieldset in fieldset (Vps_Form_CheckboxFieldsetInFieldset_Test)
 */
class Vps_Form_CheckboxFieldsetInCards_Test extends Vps_Test_SeleniumTestCase
{
    public function test()
    {
        $this->open('/vps/test/vps_form_checkbox-fieldset-in-cards_test');
        $this->waitForConnections();
        $this->click("//div[@class='x-column-inner']/div[2]//img[@class='x-form-radio']");
        $this->click('//input[@type="checkbox"]');
        $this->click("//div[@class='x-column-inner']/div[1]//img[@class='x-form-radio']");
        $this->click("//div[@class='x-column-inner']/div[2]//img[@class='x-form-radio']");
        $this->click("//button[text()='".trlVps('Save')."']");
        $this->waitForConnections();
        $this->assertTextNotPresent(trlVps("Can't save, please fill all red underlined fields correctly."));

        $this->open('/vps/test/vps_form_checkbox-fieldset-in-cards_test');
        $this->waitForConnections();
        $this->click("//div[@class='x-column-inner']/div[3]//img[@class='x-form-radio']");
        $this->assertElementNotPresent('.vps-test-subcards[disabled]');

        $this->open('/vps/test/vps_form_checkbox-fieldset-in-cards_test');
        $this->waitForConnections();
        $this->click("//div[@class='x-column-inner']/div[4]//img[@class='x-form-radio']");
        $this->assertElementNotPresent('.vps-test-text4[disabled]');
    }

}
