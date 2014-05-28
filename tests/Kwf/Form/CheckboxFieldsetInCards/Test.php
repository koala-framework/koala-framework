<?php
/**
 * @group slow
 * @group selenium
 * @group Kwf_Form_FieldsetInCards
 *
 * Testen den Bug der auftritt wenn in einem Cards ein fieldset ist das per checkbox
 * deaktiviert werden kann.
 * Wenn auf eine Card umgeschalten wird, werden rekursiv alle Felder in dieser Card
 * enabled, was dann auch die felder in dem Fieldset aktiviert - obwohl es möglicherweise
 * nicht angehakt ist.
 * Dadurch wird dann im deaktivierten fieldset validiert.
 *
 * genau das gleiche mit fieldset in fieldset (Kwf_Form_CheckboxFieldsetInFieldset_Test)
 */
class Kwf_Form_CheckboxFieldsetInCards_Test extends Kwf_Test_SeleniumTestCase
{
    public function test()
    {
        $this->open('/kwf/test/kwf_form_checkbox-fieldset-in-cards_test');
        $this->waitForConnections();
        $this->click("//div[@class='x2-column-inner']/div[2]//img[@class='x2-form-radio']");
        $this->click('//input[@type="checkbox"]');
        $this->click("//div[@class='x2-column-inner']/div[1]//img[@class='x2-form-radio']");
        $this->click("//div[@class='x2-column-inner']/div[2]//img[@class='x2-form-radio']");
        $this->click("//button[text()='".trlKwf('Save')."']");
        $this->waitForConnections();
        $this->assertTextNotPresent(trlKwf("Can't save, please fill all red underlined fields correctly."));

        $this->open('/kwf/test/kwf_form_checkbox-fieldset-in-cards_test');
        $this->waitForConnections();
        $this->click("//div[@class='x2-column-inner']/div[3]//img[@class='x2-form-radio']");
        $this->assertElementNotPresent('.kwf-test-subcards[disabled]');

        $this->open('/kwf/test/kwf_form_checkbox-fieldset-in-cards_test');
        $this->waitForConnections();
        $this->click("//div[@class='x2-column-inner']/div[4]//img[@class='x2-form-radio']");
        $this->assertElementNotPresent('.kwf-test-text4[disabled]');
    }

}
