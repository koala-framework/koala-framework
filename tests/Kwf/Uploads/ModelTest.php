<?php
/**
 * @group Uploads
 */
class Kwf_Uploads_ModelTest extends Kwf_Test_TestCase
{
    private $_model;
    private $_uploadsModel;

    public function setUp()
    {
        parent::setUp();
        $this->_uploadsModel = new Kwf_Uploads_TestModel();

        $this->_model = new Kwf_Model_FnF(array(
            'dependentModels' => array('File' => array(
                'refModelClass' => 'Kwf_Uploads_TestModel',
                'column' => 'upload_id'
            )),
            'data' => array(
            )
        ));
    }

    public function testUploadFile()
    {
        $file = tempnam('/tmp', 'testupload');
        file_put_contents($file, 'fooo');
        $postData = array(
            'error' => 0,
            'tmp_name' => $file,
            'name' => 'test.txt',
            'type' => 'text/plain'
        );
        $row = $this->_uploadsModel->createRow();
        $row->uploadFile($postData);
        $this->assertFileEquals($file, $row->getFileSource());
        $this->assertEquals('text/plain', $row->mime_type);
        $this->assertEquals('test', $row->filename);
        $this->assertEquals('txt', $row->extension);
        unlink($file);
    }

    public function testCopyFile()
    {
        $file = tempnam('/tmp', 'testupload');
        file_put_contents($file, 'fooo');
        $row = $this->_uploadsModel->createRow();
        $row->copyFile($file, 'test', 'txt', 'text/plain');
        $this->assertFileEquals($file, $row->getFileSource());
        $this->assertEquals('text/plain', $row->mime_type);
        unlink($file);
    }

    public function testWriteFile()
    {
        $row = $this->_uploadsModel->createRow();
        $row->writeFile('foo', 'test', 'txt', 'text/plain');
        $this->assertStringEqualsFile($row->getFileSource(), 'foo');
    }


    public function testGetFileSource()
    {
        $row = $this->_uploadsModel->createRow();
        $row->writeFile('foo', 'foo', 'txt', 'text/plain');
        $dir = $this->_uploadsModel->getUploadDir();
        $this->assertRegExp('#^'.$dir.'/[a-z0-9]{2}/.+$#', $row->getFileSource());
        $this->assertEquals(3, $row->getFileSize());

        $row = $this->_uploadsModel->createRow();
        $row->save();
        $this->assertRegExp('#^'.$dir.'/[a-z0-9]{2}/.+$#', $row->getFileSource());
        $this->assertEquals(null, $row->getFileSize());
    }

    public function testGetFileInfo()
    {
        $row = $this->_uploadsModel->createRow();
        $row->writeFile('foo', 'foo', 'txt', 'text/plain');
        $info = $row->getFileInfo();
        $this->assertRegExp('#^[a-z0-9]{8}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{12}$#', $info['uploadId']);
        $this->assertContains('text/plain', $info['mimeType']);
        $this->assertEquals('foo', $info['filename']);
        $this->assertEquals('txt', $info['extension']);
        $this->assertEquals(3, $info['fileSize']);
        $this->assertEquals(false, $info['image']);

        $row = $this->_uploadsModel->createRow();
        $row->copyFile(KWF_PATH.'/images/welcome/ente.jpg', 'foo', 'jpg');
        $info = $row->getFileInfo();
        $this->assertRegExp('#^[a-z0-9]{8}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{12}$#', $info['uploadId']);
        $this->assertContains('image/jpeg', $info['mimeType']);
        $this->assertEquals('foo', $info['filename']);
        $this->assertEquals('jpg', $info['extension']);
        $this->assertEquals(2266, $info['fileSize']);
        $this->assertEquals(true, $info['image']);
        $this->assertEquals(54, $info['imageWidth']);
        $this->assertEquals(30, $info['imageHeight']);
    }

    public function testDeleteFile()
    {
        $row = $this->_uploadsModel->createRow();
        $row->writeFile('foo', 'foo', 'txt', 'text/plain');
        $f = $row->getFileSource();
        $this->assertTrue(file_exists($f));
        $row->delete();
        $this->assertFalse(file_exists($f));
    }

    public function testDuplicate()
    {
        //TODO
    }
}
