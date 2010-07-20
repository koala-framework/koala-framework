<?php
/**
 * @group Model
 * @group Model_Mail
 * @group Mail
 */
class Vps_Model_Mail_ModelTest extends PHPUnit_Framework_TestCase
{
    private $_fnf;

    public function setUp()
    {
        $this->_fnf = new Vps_Model_FnF(array(
            'data' => array(
                array('id' => 4, 'save_date' => '2008-10-29 08:15:00', 'is_spam' => 0, serialize(array()), serialize(array()))
            ),
            'columns' => array('id', 'save_date', 'is_spam', 'mail_sent', 'serialized_mail_vars', 'serialized_mail_essentials'),
            'primaryKey' => 'id'
        ));
    }

    public function testRow()
    {
        $model = new Vps_Model_Mail(array(
            'proxyModel' => $this->_fnf,
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

        // händisches MockMail weil nur der class-name übergeben werden darf, kein objekt
        Vps_Model_Mail_MockMail::resetCalls();

        $model = new Vps_Model_Mail(array(
            'proxyModel' => $this->_fnf,
            'tpl' => 'UserActivation',
            'mailerClass' => 'Vps_Model_Mail_MockMail',
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

        $addRow = $addStore->getRows()->current();
        $this->assertEquals('bar', $addRow->foo);
        $this->assertEquals('bar2', $addRow->foo2);
        $this->assertEquals('foo sub', $addRow->subject);
    }

    public function testMail()
    {
        // händisches MockMail weil nur der class-name übergeben werden darf, kein objekt
        Vps_Model_Mail_MockMail::resetCalls();

        $model = new Vps_Model_Mail(array(
            'proxyModel' => $this->_fnf,
            'tpl' => 'UserActivation',
            'mailerClass' => 'Vps_Model_Mail_MockMail',
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
        $this->assertEquals('Test subject', Vps_Model_Mail_MockMail::$data['subject']);

        $this->assertEquals(2, count(Vps_Model_Mail_MockMail::$addToCalled));
        $this->assertEquals(array(
            array('markus@vivid.vps', ''),
            array('markus2@vivid.vps', 'm2')
        ), Vps_Model_Mail_MockMail::$addToCalled);

        $this->assertEquals(
            array('from@vivid.vps', 'Vps'),
            Vps_Model_Mail_MockMail::$setFromCalled
        );

        $this->assertEquals(array(
                array('cc@vivid.vps', 'cc')
            ), Vps_Model_Mail_MockMail::$addCcCalled
        );

        $this->assertEquals(
            array('bcc@vivid.vps'),
            Vps_Model_Mail_MockMail::$addBccCalled
        );

        $this->assertContains(
            array('X-MyHeader', '321', false),
            Vps_Model_Mail_MockMail::$addHeaderCalled
        );

    }
}

