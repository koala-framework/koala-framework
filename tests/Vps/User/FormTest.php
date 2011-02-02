<?php
/**
 * @group slow
 * @group selenium
 * @group User
 * @group User_Form
 */
class Vps_User_FormTest extends Vps_Test_SeleniumTestCase
{
    public function setUp()
    {
        parent::setUp();
        Vps_Test_SeparateDb::createSeparateTestDb(dirname(__FILE__).'/bootstrap.sql');
    }

    public function tearDown()
    {
        Vps_Test_SeparateDb::restoreTestDb();
        parent::tearDown();
    }

    public function testForm()
    {
        $email = 'seltest_abc1@vivid-planet.com';

        // user wieder löschen wenns ihn gibt sonst funzt der test das nächste mal nimmer
        $allModel = Vps_Model_Abstract::getInstance('Vps_User_All_Model');
        $allRow = $allModel->getRow($allModel->select()
            ->whereEquals('email', $email)
        );

        if ($allRow && $allRow->id) {
            $model = Vps_Model_Abstract::getInstance('Vps_User_Relation_Model');
            $row = $model->getRow($model->select()
                ->whereEquals('user_id', $allRow->id)
            );
            if ($row) $row->delete();
        }

        $this->open('/vps/test/vps_user_form?testDb='.Vps_Test_SeparateDb::getDbName());
        $this->waitForConnections();
        $this->type("//input[contains(@name, 'email')]", $email);
        $this->type("//input[contains(@name, 'firstname')]", 'Sel');
        $this->type("//input[contains(@name, 'lastname')]", 'Test');
        $this->type("//input[contains(@name, 'title')]", 'ttl');
        $this->click("//img[contains(@class, 'x-form-arrow-trigger')]");
        $this->click("//div[contains(@class, 'x-combo-list-inner')]/div[contains(@class, 'x-combo-list-item')]");
        $this->click("//button[contains(@class, 'x-btn-text')]");

        sleep(1);
        $this->waitForConnections();



    }
}
