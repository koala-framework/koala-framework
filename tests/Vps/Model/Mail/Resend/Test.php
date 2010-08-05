<?php
/**
 * @group Model
 * @group Model_Mail
 * @group Model_Mail_Resend
 * @group Mail
 */
class Vps_Model_Mail_Resend_Test extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();
        Vps_Test_SeparateDb::createSeparateTestDb(dirname(__FILE__).'/bootstrap.sql');
    }

    public function tearDown()
    {
        $this->assertFalse(Vps_User_Model::isLockedCreateUser());
//         Vps_Test_SeparateDb::restoreTestDb();
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
        $model = new Vps_Model_Mail(array(
            'tpl' => 'UserDeleted',
        ));

        $row = $model->createRow();
        $row->fullname = 'Test Tester';
        $row->webUrl = 'http://mytesturl.vivid';
        $row->applicationName = 'myapp';

        $row->addTo('mytest@vivid.vps');
        $row->subject = 'myblubb subject';

        $row->save();

        $mailFirst = $this->_getLatestMail();

        $row->sendMail();

        $mailSecond = $this->_getLatestMail();

        $this->assertNotEquals($mailFirst->id, $mailSecond->id);
        $this->assertEquals($mailFirst->subject, $mailSecond->subject);
        $this->assertEquals($mailFirst->body_text->getContent(), $mailSecond->body_text->getContent());
        $this->assertEquals($mailFirst->body_html->getContent(), $mailSecond->body_html->getContent());
    }
}
