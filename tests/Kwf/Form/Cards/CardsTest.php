<?php
/**
 * @group selenium
 * @group slow
 * @group Cards
 */
class Kwf_Form_Cards_CardsTest extends Kwf_Test_SeleniumTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->setTimeout(300000);
    }

    public function testCards()
    {

        $this->open('/kwf/test/kwf_form_cards_test?id=4');
        $this->waitForConnections();
        $this->assertFalse($this->isVisible("//input[@name = 'firstname']"));
        $this->assertTrue($this->isVisible("//input[@name = 'lastname']"));
        $this->assertEquals('foo', $this->getValue("//input[@name = 'lastname']"));

        $this->open('/kwf/test/kwf_form_cards_test?id=1');
        $this->waitForConnections();
        $this->assertFalse($this->isVisible("//input[@name = 'lastname']"));
        $this->assertTrue($this->isVisible("//input[@name = 'firstname']"));
        $this->assertEquals('Max', $this->getValue("//input[@name = 'firstname']"));

        $this->type("//input[@name = 'firstname']", 'newName');
        $this->click("//button[text()='".trlKwf('Save')."']");
        $this->waitForConnections();
        $this->open('/kwf/test/kwf_form_cards_test/get-model-data');
        $this->assertBodyText('newName');

    }


}