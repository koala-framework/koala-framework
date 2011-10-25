<?php
/**
 * @group selenium
 * @group slow
 * @group Cards
 * @group CardsRealModels
 */
class Kwf_Form_CardsRealModels_CardsTest extends Kwf_Test_SeleniumTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->setTimeout(300000);
        Kwf_Test_SeparateDb::createSeparateTestDb(dirname(__FILE__).'/bootstrap.sql');
    }

    public function tearDown()
    {
        Kwf_Test_SeparateDb::restoreTestDb();
        parent::tearDown();
    }

    public function testCards()
    {
        $this->open('/kwf/test/kwf_form_cards-real-models_test?id=1&testDb='.Kwf_Test_SeparateDb::getDbName());
        $this->waitForConnections();
        $this->assertFalse($this->isVisible("//input[@name = 'firstname']"));
        $this->assertTrue($this->isVisible("//input[@name = 'lastname']"));
        $this->assertEquals('lasttest', $this->getValue("//input[@name = 'lastname']"));

        $this->type("//input[@name = 'lastname']", 'new last value');
        $this->click("//button[text()='".trlKwf('Save')."']");
        $this->waitForConnections();
        $this->open('/kwf/test/kwf_form_cards-real-models_test?id=1&testDb='.Kwf_Test_SeparateDb::getDbName());
        $this->waitForConnections();
        $this->assertEquals('new last value', $this->getValue("//input[@name = 'lastname']"));
    }
}
