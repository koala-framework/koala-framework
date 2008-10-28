<?php
/**
 * @group Form_File
 */
class Vps_Form_File_FileTest extends PHPUnit_Framework_TestCase
{
    private $_model;
    private $_uploadsModel;
    private $_row;
    private $_field;

    public function setUp()
    {
        Vps_Model_Abstract::clearInstances();
        $this->_uploadsModel = Vps_Model_Abstract::getInstance('Vps_Form_File_UploadsTestModel');

        $this->_model = new Vps_Model_FnF(array(
            'referenceMap' => array('File' => array(
                'refModelClass' => 'Vps_Form_File_UploadsTestModel',
                'column' => 'upload_id'
            )),
            'data' => array(
            )
        ));
        $fRow = $this->_uploadsModel->createRow()->writeFile('asdf', 'foo', 'txt');
        $this->_uploadsModel->createRow()->writeFile('asdf1', 'foo1', 'txt');

        $this->_row = $this->_model->createRow();
        $this->_row->upload_id = $fRow->id;
        $this->_row->save();
        $this->_field = new Vps_Form_Field_File('File');
    }
    public function tearDown()
    {
        $dir = $this->_uploadsModel->getUploadDir();
        if (substr($dir, 0, 4)=='/tmp') {
            system('rm -r '.$dir);
        }
    }

    public function testProcessInputNothingUploaded()
    {
        $input = array(
            'File_upload_id' => 1
        );
        $data = $this->_field->processInput($this->_row, $input);
        $this->assertEquals($data, array('File' => '1'));

        $input = array(
            'File_upload_id' => 1,
            'File' => array(
                'error' => UPLOAD_ERR_NO_FILE,
                'tmp_name' => false
            )
        );
        $data = $this->_field->processInput($this->_row, $input);
        $this->assertEquals($data, array('File' => '1'));
    }

    public function testProcessInputFileUploaded()
    {
        $file = tempnam('/tmp', 'uploadtest');
        file_put_contents('foo', $file);
        $input = array(
            'File_upload_id' => 1,
            'File' => array(
                'error' => 0,
                'tmp_name' => $file,
                'type' => 'plain/text',
                'name' => 'test.txt'
            )
        );
        $data = $this->_field->processInput($this->_row, $input);
        $this->assertEquals($data, array('File' => '3'));
    }

    public function testProcessInputFileUploadedNothingExisting()
    {
        $file = tempnam('/tmp', 'uploadtest');
        file_put_contents('foo', $file);
        $input = array(
            'File' => array(
                'error' => 0,
                'tmp_name' => $file,
                'type' => 'plain/text',
                'name' => 'test.txt'
            )
        );
        $data = $this->_field->processInput($this->_row, $input);
        $this->assertEquals($data, array('File' => '3'));
    }

    public function testProcessInputFileDelete()
    {
        $input = array(
            'File_upload_id' => 1,
            'File_del' => 1,
        );
        $data = $this->_field->processInput($this->_row, $input);
        $this->assertEquals($data, array('File' => null));
    }

    public function testProcessInputFileDeleteAndUploaded()
    {
        $file = tempnam('/tmp', 'uploadtest');
        file_put_contents('foo', $file);

        $input = array(
            'File_upload_id' => 1,
            'File_del' => 1,
            'File' => array(
                'error' => 0,
                'tmp_name' => $file,
                'type' => 'plain/text',
                'name' => 'test.txt'
            )
        );
        $data = $this->_field->processInput($this->_row, $input);
        $this->assertEquals($data, array('File' => null));
    }

    public function testProcessInputBackend()
    {
        $input = array(
            'File' => 1,

        );
        $data = $this->_field->processInput($this->_row, $input);
        $this->assertEquals($data, array('File' => 1));
    }

    public function testValidate()
    {
        $row = $this->_model->createRow();
        $this->assertEquals(array(), $this->_field->validate($row, array('File'=>1)));

        $this->_field->setAllowOnlyImages(true);
        $this->assertEquals(1, count($this->_field->validate($row, array('File'=>1))));

        $fRow = $this->_uploadsModel->createRow()->writeFile('', 'foo', 'jpg', 'image/jpeg');
        $this->assertEquals(array(), $this->_field->validate($row, array('File'=>$fRow->id)));
    }

    public function testLoad()
    {
        $post = array(
            'File' => 1
        );
        $data = $this->_field->load($this->_row, $post);
        $i = $this->_uploadsModel->getRow(1)->getFileInfo();
        $this->assertEquals(array('File'=>$i), $data);

        $post = array(
            'File' => 2
        );
        $data = $this->_field->load($this->_row, $post);
        $i = $this->_uploadsModel->getRow(2)->getFileInfo();
        $this->assertEquals(array('File'=>$i), $data);
    }
}
