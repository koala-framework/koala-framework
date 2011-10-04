<?php
/**
 * @group selenium
 * @group slow
 * @group Cards
 * @group Cards_NotBlank
 */
class Vps_Form_Cards_NotAllowBlank_CardsTest extends Vps_Test_SeleniumTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->setTimeout(300000);
    }

    public function testCards()
    {
        $this->open('/vps/test/vps_form_cards_not-allow-blank_test?id=4');
        $this->waitForConnections();
        $this->click("//button[text()='".trlVps('Save')."']");
        $this->waitForConnections();
        $this->assertTextNotPresent(trlVps("Can't save, please fill all red underlined fields correctly."));

        $this->open('/vps/test/vps_form_cards_not-allow-blank_test?id=4');
        $this->waitForConnections();
        $this->click("//div[@class='x-column-inner']/div[2]//img[@class='x-form-radio']");
        $this->click("//button[text()='".trlVps('Save')."']");
        $this->waitForConnections();
        $this->assertTextPresent(trlVps("Can't save, please fill all red underlined fields correctly."));

        $this->open('/vps/test/vps_form_cards_not-allow-blank_test?id=4');
        $this->waitForConnections();
        $this->click("//div[@class='x-column-inner']/div[2]//img[@class='x-form-radio']");
        $this->click("//img[contains(@class, 'x-form-arrow-trigger')]");
        $this->click("//div[@class='x-combo-list-inner']/div[2]");
        $this->click("//button[text()='".trlVps('Save')."']");
        $this->waitForConnections();
        $this->assertTextNotPresent(trlVps("Can't save, please fill all red underlined fields correctly."));
    }
}
