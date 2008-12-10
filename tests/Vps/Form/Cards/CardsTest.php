<?php
/**
 * @group selenium
 * @group Cards
 */
class Vps_Form_Cards_CardsTest extends Vps_Test_SeleniumTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->setTimeout(120000);
    }

    public function testCards()
    {

        $this->open('/vps/test/vps_form_cards_test?id=4');
        $this->click("//input[@name = 'lastname']");
        $this->waitForConnections();
        $this->open('/vps/test/vps_form_cards_test?id=1');
        $this->click("//input[@name = 'firstname']");
        $this->waitForConnections();
        $this->type("//input[@name = 'firstname']", 'newName');
        $this->click("//button[text()='".trlVps('Save')."']");
        $this->open('/vps/test/vps_form_cards_test/get-model-data');
        $this->assertTextPresent('newName');
        sleep(5);

    }


}