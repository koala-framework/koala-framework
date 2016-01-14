<?php
/**
 * @group Form_File
 */
class Kwf_Form_File_FileTest extends Kwf_Test_TestCase
{
    private $_model;
    private $_uploadsModel;
    private $_row;
    private $_field;
    private $_uploadId1;
    private $_uploadId2;

    public function setUp()
    {
        parent::setUp();
        $this->_uploadsModel = Kwf_Model_Abstract::getInstance('Kwf_Form_File_UploadsTestModel');

        $this->_model = new Kwf_Model_FnF(array(
            'referenceMap' => array('File' => array(
                'refModelClass' => 'Kwf_Form_File_UploadsTestModel',
                'column' => 'upload_id'
            )),
            'data' => array(
            )
        ));
        $fRow = $this->_uploadsModel->createRow()->writeFile('asdf', 'foo', 'txt', 'text/plain');
        $this->_uploadId1 = $fRow->id;
        $r = $this->_uploadsModel->createRow()->writeFile('asdf1', 'foo1', 'txt', 'text/plain');
        $this->_uploadId2 = $r->id;

        $this->_row = $this->_model->createRow();
        $this->_row->upload_id = $fRow->id;
        $this->_row->save();
        $this->_field = new Kwf_Form_Field_File('File');
    }

    public function testProcessInputNothingUploaded()
    {
        $uploadRow = $this->_uploadsModel->getRow($this->_uploadId1);
        $input = array(
            'File_upload_id' => $uploadRow->id.'_'.$uploadRow->getHashKey()
        );
        $data = $this->_field->processInput($this->_row, $input);

        $this->assertEquals($data, array('File' => $this->_uploadId1));

        $input = array(
            'File_upload_id' => $uploadRow->id.'_'.$uploadRow->getHashKey(),
            'File' => array(
                'error' => UPLOAD_ERR_NO_FILE,
                'tmp_name' => false
            )
        );
        $data = $this->_field->processInput($this->_row, $input);
        $this->assertEquals($data, array('File' => $this->_uploadId1));
    }

    public function testProcessInputNothingUploadedEmpty()
    {
        $input = array(
            'File_upload_id' => ''
        );
        $data = $this->_field->processInput($this->_row, $input);
        $this->assertSame($data, array('File' => null));
    }

    public function testProcessInputEmpty()
    {
        $input = array();
        $data = $this->_field->processInput($this->_row, $input);
        $this->assertSame($data, array());
    }

    public function testProcessInputFileUploaded()
    {
        $file = tempnam('/tmp', 'uploadtest');
        file_put_contents($file, 'foo');
        $input = array(
            'File_upload_id' => $this->_uploadId1,
            'File' => array(
                'error' => 0,
                'tmp_name' => $file,
                'type' => 'plain/text',
                'name' => 'test.txt'
            )
        );
        $data = $this->_field->processInput($this->_row, $input);
        $s = new Kwf_Model_Select();
        $s->whereNotEquals('id', array($this->_uploadId1, $this->_uploadId2));
        $r = $this->_uploadsModel->getRow($s);
        $this->assertEquals($data, array('File' => $r->id));
    }

    public function testProcessInputFileUploadedNothingExisting()
    {
        $file = tempnam('/tmp', 'uploadtest');
        file_put_contents($file, 'foo');
        $input = array(
            'File' => array(
                'error' => 0,
                'tmp_name' => $file,
                'type' => 'plain/text',
                'name' => 'test.txt'
            )
        );
        $data = $this->_field->processInput($this->_row, $input);
        $s = new Kwf_Model_Select();
        $s->whereNotEquals('id', array($this->_uploadId1, $this->_uploadId2));
        $r = $this->_uploadsModel->getRow($s);
        $this->assertEquals($data, array('File' => $r->id));
    }

    public function testProcessInputFileDelete()
    {
        $uploadRow = $this->_uploadsModel->getRow($this->_uploadId1);
        $input = array(
            'File_upload_id' => $uploadRow->id.'_'.$uploadRow->getHashKey(),
            'File_del' => '1',
        );
        $data = $this->_field->processInput($this->_row, $input);
        $this->assertSame($data, array('File' => null));
    }

    public function testProcessInputFileDeleteAndUploaded()
    {
        $file = tempnam('/tmp', 'uploadtest');
        file_put_contents($file, 'foo');

        $input = array(
            'File_upload_id' => $this->_uploadId1,
            'File_del' => '1',
            'File' => array(
                'error' => 0,
                'tmp_name' => $file,
                'type' => 'plain/text',
                'name' => 'test.txt'
            )
        );
        $data = $this->_field->processInput($this->_row, $input);
        $this->assertSame($data, array('File' => null));
    }

    public function testProcessInputBackend()
    {
        $input = array(
            'File' => $this->_uploadId1,

        );
        $data = $this->_field->processInput($this->_row, $input);
        $this->assertEquals($data, array('File' => $this->_uploadId1));
    }

    public function testProcessInputBackendEmpty()
    {
        $input = array(
            'File' => ''
        );
        $data = $this->_field->processInput($this->_row, $input);
        $this->assertSame($data, array('File' => null));
    }

    public function testValidate()
    {
        $row = $this->_model->createRow();
        $this->assertEquals(array(), $this->_field->validate($row, array('File'=>$this->_uploadId1)));

        $this->_field->setAllowOnlyImages(true);
        $this->assertEquals(1, count($this->_field->validate($row, array('File'=>$this->_uploadId1))));

        $fRow = $this->_uploadsModel->createRow()->writeFile('', 'foo', 'jpg', 'image/jpeg');
        $this->assertEquals(array(), $this->_field->validate($row, array('File'=>$fRow->id)));
    }

    public function testLoad()
    {
        $post = array(
            'File' => $this->_uploadId1
        );
        $data = $this->_field->load($this->_row, $post);
        $i = $this->_uploadsModel->getRow($this->_uploadId1)->getFileInfo();
        $this->assertEquals(array('File'=>$i), $data);

        $post = array(
            'File' => $this->_uploadId2
        );
        $data = $this->_field->load($this->_row, $post);
        $i = $this->_uploadsModel->getRow($this->_uploadId2)->getFileInfo();
        $this->assertEquals(array('File'=>$i), $data);

        $post = array(
            'File' => null
        );
        $data = $this->_field->load($this->_row, $post);
        $this->assertEquals(array('File'=>''), $data);
    }

    public function testPrepareSave()
    {
        $post = array(
            'File' => $this->_uploadId1
        );
        $this->_field->prepareSave($this->_row, $post);
        $this->assertEquals($this->_uploadId1, $this->_row->upload_id);

        $post = array(
            'File' => $this->_uploadId2
        );
        $this->_field->prepareSave($this->_row, $post);
        $this->assertEquals($this->_uploadId2, $this->_row->upload_id);

        $post = array(
            'File' => null
        );
        $this->_field->prepareSave($this->_row, $post);
        $this->assertEquals(null, $this->_row->upload_id);
    }

    public function testSaveDisabled1()
    {
        $this->_field->setSave(false);

        $post = array(
            'File_upload_id' => $this->_uploadId1,
            'File_del' => 1,
            'File' => array(
                'error' => 0,
                'tmp_name' => __FILE__,
                'type' => 'plain/text',
                'name' => 'test.txt'
            )
        );
        $post = $this->_field->processInput($this->_row, $post);

        $this->_field->setAllowOnlyImages(true);
        $data = $this->_field->validate($this->_row, $post);
        $this->assertEquals(array(), $data);

        $data = $this->_field->load($this->_row, array());
        $this->assertEquals(array(), $data);

        $post = array(
            'File' => $this->_uploadId2
        );
        $data = $this->_field->prepareSave($this->_row, $post);
        $this->assertEquals($this->_uploadId1, $this->_row->upload_id);
    }
}
