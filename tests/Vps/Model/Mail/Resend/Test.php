<?php
/**
 * @group Model
 * @group Model_Mail
 * @group Model_Mail_Resend
 * @group Mail
 * @group slow
 */
class Vps_Model_Mail_Resend_Test extends Vps_Test_TestCase
{
    public function setUp()
    {
        parent::setUp();
        Vps_Test_SeparateDb::createSeparateTestDb(dirname(__FILE__).'/bootstrap.sql');
    }

    public function tearDown()
    {
        $this->assertFalse(Vps_User_Model::isLockedCreateUser());
        Vps_Test_SeparateDb::restoreTestDb();
        parent::tearDown();
    }

    protected function _getLatestMail($select = null)
    {
        $m = Vps_Model_Abstract::getInstance('Vps_Util_Model_MailLog');
        if (!$select) $select = $m->select();
        $select->order('id', 'DESC');
        $select->limit(1);
        return $m->getRow($select);
    }

    public function testText()
    {
        $uploads = Vps_Model_Abstract::getInstance('Vps_Uploads_TestModel');

        $model = new Vps_Model_Mail(array(
            'tpl' => 'tests/Vps/Model/Mail/Resend/Mail',
            'spamFields' => array()
        ));
        $model->setAttachmentSaveFolder($uploads->getUploadDir().'/mailattachments');
        if (!file_exists($uploads->getUploadDir().'/mailattachments') || !is_dir($uploads->getUploadDir().'/mailattachments')) {
            mkdir($uploads->getUploadDir().'/mailattachments');
        }

        $ulRow = $uploads->createRow();
        $ulRow->writeFile('foocontent', 'modelmail', 'txt');

        $row = $model->createRow();
        $row->fullname = 'Test Tester';
        $row->imagepath = realpath(dirname(__FILE__));

        $row->addTo('mytest@vivid.vps', 'Test1');
        $row->addTo('mytest2@vivid.vps', 'Test2');
        $row->setFrom('test-from@vivid.vps', 'Test From');
        $row->addBcc('test-bcc1@vivid.vps');
        $row->addBcc('test-bcc2@vivid.vps');
        $row->addCc('test-cc1@vivid.vps', 'Test cc 1');
        $row->addCc('test-cc2@vivid.vps', 'Test cc 2');
        $row->setReturnPath('test-return@vivid.vps');
        $row->subject = 'myblubb subject';

        $row->addAttachment(realpath(dirname(__FILE__).'/rtr.png'));
        $row->addAttachment(realpath(dirname(__FILE__).'/rtr.png'), 'rtrtest.png');

        $row->addAttachment($ulRow);
        $row->addAttachment($ulRow, 'myfoomodelmail.txt');

        $row->save();
        $mailFirst = $this->_getLatestMail();

        $row->resendMail();
        $mailSecond = $this->_getLatestMail();

        $this->assertNotEquals($mailFirst->id, $mailSecond->id);
        $this->assertEquals($mailFirst->subject, $mailSecond->subject);
        $this->assertEquals($mailFirst->to, $mailSecond->to);
        $this->assertEquals($mailFirst->from, $mailSecond->from);
        $this->assertEquals($mailFirst->return_path, $mailSecond->return_path);
        $this->assertEquals($mailFirst->bcc, $mailSecond->bcc);
        $this->assertEquals($mailFirst->cc, $mailSecond->cc);
        $this->assertEquals($mailFirst->attachment_filenames, $mailSecond->attachment_filenames);
        $this->assertEquals('rtr.png;rtrtest.png;modelmail.txt;myfoomodelmail.txt;paragraphDelete.png', $mailSecond->attachment_filenames);
        $this->assertEquals($mailFirst->body_text->getContent(), $mailSecond->body_text->getContent());
        $this->assertEquals($mailFirst->body_html->getContent(), $mailSecond->body_html->getContent());
    }
}
