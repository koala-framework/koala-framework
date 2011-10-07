<?php
/**
 * @group Form_TextField
 */
class Kwf_Form_TextFieldTest extends Kwf_Test_TestCase
{
    private $_textField;

    public function setUp()
    {
        parent::setUp();
        $this->_textField = new Kwf_Form_Field_TextField('test12');
    }

    public function testLoad()
    {
        $model = new Kwf_Model_FnF();
        $row = $model->createRow();
        $row->test12 = 'foobar';
        $data = $this->_textField->load($row);
        $this->assertEquals(array('test12' => 'foobar'), $data);

        $data = $this->_textField->load($row, array('test12' => 'loaded', 'garbage2' => 'foo'));
        $this->assertEquals(array('test12' => 'loaded'), $data);

        $data = $this->_textField->load($row, array());
        $this->assertEquals(array('test12' => 'foobar'), $data);
    }

    public function testPrepareSave()
    {
        $model = new Kwf_Model_FnF();
        $row = $model->createRow();
        $row->test12 = 'foobar';
        $this->_textField->prepareSave($row, array('test12' => 'foobar'));
        $this->assertEquals('foobar', $row->test12);
    }
    public function testValidate()
    {
        $model = new Kwf_Model_FnF();
        $row = $model->createRow();

        $this->assertEquals(array(), $this->_textField->validate($row, array('test12' => 'foobar')));

        // eigenes feld weil beim vorigen _addValidators schon augerufen wurde
        $newTextField = new Kwf_Form_Field_TextField('test123');
        $newTextField->setVtype('email');
        $this->assertEquals(1, count($newTextField->validate($row, array('test123' => 'foobar'))));
        $this->assertEquals(0, count($newTextField->validate($row, array('test123' => 'foo@bar.com'))));
    }
}
