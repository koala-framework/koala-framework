<?php
/**
 * @group selenium
 * @group slow
 * @group Cards
 * @group CardsRealModels
 */
class Vps_Form_CardsRealModels_CardsTest extends Vps_Test_SeleniumTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->setTimeout(300000);
        Vps_Test_SeparateDb::createSeparateTestDb(dirname(__FILE__).'/bootstrap.sql');
    }

    public function tearDown()
    {
        Vps_Test_SeparateDb::restoreTestDb();
        parent::tearDown();
    }

    public function testCards()
    {
        $this->open('/vps/test/vps_form_cards-real-models_test?id=1&testDb='.Vps_Test_SeparateDb::getDbName());
        $this->waitForConnections();
        $this->assertFalse($this->isVisible("//input[@name = 'firstname']"));
        $this->assertTrue($this->isVisible("//input[@name = 'lastname']"));
        $this->assertEquals('lasttest', $this->getValue("//input[@name = 'lastname']"));

        $this->type("//input[@name = 'lastname']", 'new last value');
        $this->click("//button[text()='".trlVps('Save')."']");
        $this->waitForConnections();
        $this->open('/vps/test/vps_form_cards-real-models_test?id=1&testDb='.Vps_Test_SeparateDb::getDbName());
        $this->waitForConnections();
        $this->assertEquals('new last value', $this->getValue("//input[@name = 'lastname']"));
    }
}
