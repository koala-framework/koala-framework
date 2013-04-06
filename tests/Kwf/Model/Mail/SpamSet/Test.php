<?php
/**
 * @group Model
 * @group Model_Mail
 * @group Model_Mail_SpamSet
 */
class Kwf_Model_Mail_SpamSet_Test extends Kwf_Test_TestCase
{
    public function setUp()
    {
        parent::setUp();
        Kwf_Test_SeparateDb::createSeparateTestDb(dirname(__FILE__).'/../bootstrap.sql');
        Kwf_Util_Check_Spam::setBackend(new Kwf_Model_Mail_SpamSet_TestSpamCheckBackend());
    }

    public function tearDown()
    {
        Kwf_Test_SeparateDb::restoreTestDb();
        Kwf_Util_Check_Spam::setBackend(null);
        parent::tearDown();
    }

    public function testSpamSetController()
    {
        $model = new Kwf_Model_Mail(array(
            'tpl' => 'UserActivation'
        ));
        $row = $model->createRow();
        $row->addTo('markus@vivid.kwf');
        $row->subject = 'Buy cheap viagra';
        $row->sent_mail_content_text = "cheap viagra cheap cialis buy now cheap viagra cheap cialis buy now\ncheap viagra cheap cialis buy now cheap viagra cheap cialis buy now";
        $row->save();

        $transport = $this->getMock('Zend_Mail_Transport_Abstract', array('_sendMail'));
        $transport->expects($this->never())
                 ->method('_sendMail');
        $ret = Kwf_Controller_Action_Spam_SetController::sendSpammedMail($row->id, 'xx'.Kwf_Util_Check_Spam::getSpamKey($row), $transport);
        $this->assertFalse($ret);

        $transport = $this->getMock('Zend_Mail_Transport_Abstract', array('_sendMail'));
        $transport->expects($this->never())
                 ->method('_sendMail');
        $ret = Kwf_Controller_Action_Spam_SetController::sendSpammedMail($row->id.'9999999999999999999', Kwf_Util_Check_Spam::getSpamKey($row), $transport);
        $this->assertFalse($ret);

        $transport = $this->getMock('Zend_Mail_Transport_Abstract', array('_sendMail'));
        $transport->expects($this->once())
                 ->method('_sendMail');
        $ret = Kwf_Controller_Action_Spam_SetController::sendSpammedMail($row->id, Kwf_Util_Check_Spam::getSpamKey($row), $transport);
        $this->assertTrue($ret);

        $transport = $this->getMock('Zend_Mail_Transport_Abstract', array('_sendMail'));
        $transport->expects($this->never())
                 ->method('_sendMail');
        $ret = Kwf_Controller_Action_Spam_SetController::sendSpammedMail($row->id, Kwf_Util_Check_Spam::getSpamKey($row), $transport);
        $this->assertFalse($ret);
    }
}
