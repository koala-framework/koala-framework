<?php
/**
 * @group Model
 * @group Model_Mail
 * @group Model_Mail_SpamSet
 */
class Vps_Model_Mail_SpamSet_Test extends Vps_Test_TestCase
{
    public function setUp()
    {
        parent::setUp();
        Vps_Test_SeparateDb::createSeparateTestDb(dirname(__FILE__).'/../bootstrap.sql');
        Vps_Util_Check_Spam::setBackend(new Vps_Model_Mail_SpamSet_TestSpamCheckBackend());
    }

    public function tearDown()
    {
        Vps_Test_SeparateDb::restoreTestDb();
        Vps_Util_Check_Spam::setBackend(null);
        parent::tearDown();
    }

    public function testSpamSetController()
    {
        $model = new Vps_Model_Mail(array(
            'tpl' => 'UserActivation'
        ));
        $row = $model->createRow();
        $row->addTo('markus@vivid.vps');
        $row->subject = 'Buy cheap viagra';
        $row->sent_mail_content_text = "cheap viagra cheap cialis buy now cheap viagra cheap cialis buy now\ncheap viagra cheap cialis buy now cheap viagra cheap cialis buy now";
        $row->save();

        $ret = Vps_Controller_Action_Spam_SetController::sendSpammedMail($row->id, 'xx'.Vps_Util_Check_Spam::getSpamKey($row));
        $this->assertFalse($ret);

        $ret = Vps_Controller_Action_Spam_SetController::sendSpammedMail($row->id.'9999999999999999999', Vps_Util_Check_Spam::getSpamKey($row));
        $this->assertFalse($ret);

        $ret = Vps_Controller_Action_Spam_SetController::sendSpammedMail($row->id, Vps_Util_Check_Spam::getSpamKey($row));
        $this->assertTrue($ret);
        
        $ret = Vps_Controller_Action_Spam_SetController::sendSpammedMail($row->id, Vps_Util_Check_Spam::getSpamKey($row));
        $this->assertFalse($ret);
    }
}
