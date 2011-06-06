<?php
/**
 * @group Model
 * @group Model_Mail
 * @group Mail
 */
class Vps_Model_Mail_ModelTest extends Vps_Test_TestCase
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

    public function testRow()
    {
        $model = new Vps_Model_Mail(array(
            'tpl' => 'UserActivation',
        ));

        // normal
        $row = $model->getRow(4);
        $row->save_date = 'test';
        $this->assertEquals('test', $row->save_date);

        // im mail-vars sibling
        $row->blubb = 'bla';
        $this->assertEquals('bla', $row->blubb);
    }

    public function testAdditionalStore()
    {
        $addStore = new Vps_Model_FnF();

        $model = new Vps_Model_Mail_Model_NoSend(array(
            'tpl' => 'UserActivation',
            'spamFields' => array(),
            'additionalStore' => $addStore
        ));
        $row = $model->createRow();
        $row->addTo('markus@vivid.vps');
        $row->setFrom('from@vivid.vps', 'Vps');
        $row->subject = 'foo sub';
        $row->foo = 'bar';
        $row->foo2 = 'bar2';
        $row->save();

        $addRows = $addStore->getRows();
        $this->assertEquals(1, count($addRows));

        $addRow = $addRows->current();
        $this->assertEquals('bar', $addRow->foo);
        $this->assertEquals('bar2', $addRow->foo2);
        $this->assertEquals('foo sub', $addRow->subject);
    }

    public function testMail()
    {
        $model = new Vps_Model_Mail_Model_NoSend(array(
            'tpl' => 'UserActivation',
            'spamFields' => array()
        ));
        $row = $model->createRow();
        $row->addTo('markus@vivid.vps');
        $row->addTo('markus2@vivid.vps', 'm2');
        $row->setFrom('from@vivid.vps', 'Vps');
        $row->addCc('cc@vivid.vps', 'cc');
        $row->addBcc('bcc@vivid.vps', 'bcc');
        $row->addHeader('X-MyHeader', '321');
        $row->subject = 'Test subject';
        $row->foo = 'bar';
        $row->save();

        $this->assertEquals('bar', $row->foo);
        $this->assertEquals('Test subject', $row->subject);
        $this->assertEquals('Test subject', $row->getSubject());

        $this->assertEquals(array(
            array('email' => 'markus@vivid.vps', 'name' => ''),
            array('email' => 'markus2@vivid.vps', 'name' => 'm2')
        ), $row->getTo());

        $this->assertEquals(
            array('email' => 'from@vivid.vps', 'name' => 'Vps'),
            $row->getFrom()
        );

        $this->assertEquals(array(
                array('email' => 'cc@vivid.vps', 'name' => 'cc')
            ), $row->getCc()
        );

        $this->assertEquals(
            array('bcc@vivid.vps'),
            $row->getBcc()
        );

        $this->assertEquals(
            array(array('name' => 'X-MyHeader', 'value' => '321', 'append' => false)),
            $row->getHeader()
        );
    }
}

