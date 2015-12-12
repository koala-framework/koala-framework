<?php
class Kwf_Form_File_UploadsTestModel extends Kwf_Uploads_Model
{
    public function __construct($config = array())
    {
        $config['proxyModel'] = new Kwf_Model_FnF(array(
                'columns' => array('id', 'filename', 'extension', 'mime_type', 'md5_hash', 'is_image', 'image_width', 'image_height', 'image_rotation'),
                'data'=> array(
                )
            ));
        $dir = tempnam('/tmp', 'uploadstest');
        unlink($dir);
        mkdir($dir);
        $this->setUploadDir($dir);
        parent::__construct($config);
    }
}
