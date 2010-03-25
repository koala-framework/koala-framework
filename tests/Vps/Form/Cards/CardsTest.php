<?php
/**
 * @group selenium
 * @group slow
 * @group Cards
 */
class Vps_Form_Cards_CardsTest extends Vps_Test_SeleniumTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->setTimeout(300000);
    }

    public function testCards()
    {

        $this->open('/vps/test/vps_form_cards_test?id=4');
        $this->waitForConnections();
        $this->assertFalse($this->isVisible("//input[@name = 'firstname']"));
        $this->assertTrue($this->isVisible("//input[@name = 'lastname']"));
        $this->assertEquals('foo', $this->getValue("//input[@name = 'lastname']"));

        $this->open('/vps/test/vps_form_cards_test?id=1');
        $this->waitForConnections();
        $this->assertFalse($this->isVisible("//input[@name = 'lastname']"));
        $this->assertTrue($this->isVisible("//input[@name = 'firstname']"));
        $this->assertEquals('Max', $this->getValue("//input[@name = 'firstname']"));

        $this->type("//input[@name = 'firstname']", 'newName');
        $this->click("//button[text()='".trlVps('Save')."']");
        $this->waitForConnections();
        $this->open('/vps/test/vps_form_cards_test/get-model-data');
        $this->assertBodyText('newName');

    }


}