<?php
class Kwf_Uploads_S3Test extends Kwf_Test_TestCase
{
    public function setUp()
    {
        if (!Kwf_Config::getValue('aws.key') || !Kwf_Config::getValue('aws.secret')) {
            $this->markTestSkipped();
        }
        Kwf_Registry::get('config')->aws->uploadsBucket = 'kwf-test';
        Kwf_Config::deleteValueCache('aws.uploadsBucket');
    }
    public function tearDown()
    {
        Kwf_Registry::get('config')->aws->uploadsBucket = null;
        KWf_Config::deleteValueCache('aws.uploadsBucket');
    }

    public function testIt()
    {
        $model = new Kwf_Uploads_Model(array(
            'proxyModel' => new Kwf_Model_FnF(array(
                'columns' => array('id', 'filename', 'extension', 'mime_type', 'md5_hash', 'is_image', 'image_width', 'image_height', 'image_rotation'),
                'data'=> array(
                )
            ))
        ));
        $contents = 'foobar'.time();
        $row = $model->createRow();
        $row->writeFile(
            $contents, 'foo', 'txt', 'text/plain'
        );
        $this->assertEquals(file_get_contents($row->getFileSource()), $contents);
    }
}
