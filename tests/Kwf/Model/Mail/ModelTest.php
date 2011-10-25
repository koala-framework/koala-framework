<?php
/**
 * @group Model
 * @group Model_Mail
 * @group Mail
 */
class Kwf_Model_Mail_ModelTest extends Kwf_Test_TestCase
{
    public function setUp()
    {
        parent::setUp();
        Kwf_Test_SeparateDb::createSeparateTestDb(dirname(__FILE__).'/bootstrap.sql');
    }

    public function tearDown()
    {
        Kwf_Test_SeparateDb::restoreTestDb();
        parent::tearDown();
    }

    public function testRow()
    {
        $model = new Kwf_Model_Mail(array(
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
        $addStore = new Kwf_Model_FnF();

        $model = new Kwf_Model_Mail_Model_NoSend(array(
            'tpl' => 'UserActivation',
            'spamFields' => array(),
            'additionalStore' => $addStore
        ));
        $row = $model->createRow();
        $row->addTo('markus@vivid.kwf');
        $row->setFrom('from@vivid.kwf', 'Kwf');
        $row->subject = 'foo sub';
        $row->foo = 'bar';
        $row->foo2 = 'bar2';
        // zweimal saven ist absicht, die additional row darf dennoch nur einmal angelegt werden
        $row->save();
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
        $model = new Kwf_Model_Mail_Model_NoSend(array(
            'tpl' => 'UserActivation',
            'spamFields' => array()
        ));
        $row = $model->createRow();
        $row->addTo('markus@vivid.kwf');
        $row->addTo('markus2@vivid.kwf', 'm2');
        $row->setFrom('from@vivid.kwf', 'Kwf');
        $row->addCc('cc@vivid.kwf', 'cc');
        $row->addBcc('bcc@vivid.kwf', 'bcc');
        $row->addHeader('X-MyHeader', '321');
        $row->subject = 'Test subject';
        $row->foo = 'bar';
        $row->save();

        $this->assertEquals('bar', $row->foo);
        $this->assertEquals('Test subject', $row->subject);
        $this->assertEquals('Test subject', $row->getSubject());

        $this->assertEquals(array(
            array('email' => 'markus@vivid.kwf', 'name' => ''),
            array('email' => 'markus2@vivid.kwf', 'name' => 'm2')
        ), $row->getTo());

        $this->assertEquals(
            array('email' => 'from@vivid.kwf', 'name' => 'Kwf'),
            $row->getFrom()
        );

        $this->assertEquals(array(
                array('email' => 'cc@vivid.kwf', 'name' => 'cc')
            ), $row->getCc()
        );

        $this->assertEquals(
            array('bcc@vivid.kwf'),
            $row->getBcc()
        );

        $this->assertEquals(
            array(array('name' => 'X-MyHeader', 'value' => '321', 'append' => false)),
            $row->getHeader()
        );
    }
}

