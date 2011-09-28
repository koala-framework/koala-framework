<?php
/**
 * @group Form_TextField
 */
class Vps_Form_TextFieldTest extends Vps_Test_TestCase
{
    private $_textField;

    public function setUp()
    {
        parent::setUp();
        $this->_textField = new Vps_Form_Field_TextField('test12');
    }

    public function testLoad()
    {
        $model = new Vps_Model_FnF();
        $row = $model->createRow();
        $row->test12 = 'foobar';
        $data = $this->_textField->load($row);
        $this->assertEquals($data, array('test12' => 'foobar'));

        $data = $this->_textField->load($row, array('test12' => 'loaded', 'garbage2' => 'foo'));
        $this->assertEquals($data, array('test12' => 'loaded'));

        $data = $this->_textField->load($row, array());
        $this->assertEquals($data, array('test12' => 'foobar'));
    }

    public function testPrepareSave()
    {
        $model = new Vps_Model_FnF();
        $row = $model->createRow();
        $row->test12 = 'foobar';
        $this->_textField->prepareSave($row, array('test12' => 'foobar'));
        $this->assertEquals($row->test12, 'foobar');
    }
    public function testValidate()
    {
        $model = new Vps_Model_FnF();
        $row = $model->createRow();

        $this->assertEquals($this->_textField->validate($row, array('test12' => 'foobar')), array());

        $this->_textField->setVtype('email');
        $this->assertEquals(count($this->_textField->validate($row, array('test12' => 'foobar'))), 1);
        $this->assertEquals(count($this->_textField->validate($row, array('test12' => 'foo@bar.com'))), 0);
    }
}
