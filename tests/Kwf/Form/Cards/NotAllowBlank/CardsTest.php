<?php
/**
 * @group selenium
 * @group slow
 * @group Cards
 * @group Cards_NotBlank
 */
class Kwf_Form_Cards_NotAllowBlank_CardsTest extends Kwf_Test_SeleniumTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->setTimeout(300000);
    }

    public function testCards()
    {
        $this->open('/kwf/test/kwf_form_cards_not-allow-blank_test?id=4');
        $this->waitForConnections();
        $this->click("//button[text()='".trlKwf('Save')."']");
        $this->waitForConnections();
        $this->assertTextNotPresent(trlKwf("Can't save, please fill all red underlined fields correctly."));

        $this->open('/kwf/test/kwf_form_cards_not-allow-blank_test?id=4');
        $this->waitForConnections();
        $this->click("//div[@class='x2-column-inner']/div[2]//img[@class='x2-form-radio']");
        $this->click("//button[text()='".trlKwf('Save')."']");
        $this->waitForConnections();
        $this->assertTextPresent(trlKwf("Can't save, please fill all red underlined fields correctly."));

        $this->open('/kwf/test/kwf_form_cards_not-allow-blank_test?id=4');
        $this->waitForConnections();
        $this->click("//div[@class='x2-column-inner']/div[2]//img[@class='x2-form-radio']");
        $this->click("//img[contains(@class, 'x2-form-arrow-trigger')]");
        $this->click("//div[@class='x2-combo-list-inner']/div[2]");
        $this->click("//button[text()='".trlKwf('Save')."']");
        $this->waitForConnections();
        $this->assertTextNotPresent(trlKwf("Can't save, please fill all red underlined fields correctly."));
    }
}
