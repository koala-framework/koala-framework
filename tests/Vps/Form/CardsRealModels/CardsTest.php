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
    }
}
