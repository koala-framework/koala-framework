<?php
class Vpc_Composite_ImagesEnlarge_ImageEnlarge_UploadsModel extends Vps_Uploads_Model
{
    public function __construct($config = array())
    {
        $config['proxyModel'] = new Vps_Model_FnF(array(
                'columns' => array('id', 'filename', 'extension', 'mime_type'),
                'data'=> array(
                )
            ));
        $dir = tempnam('/tmp', 'uploadstest');
        unlink($dir);
        mkdir($dir);
        $this->setUploadDir($dir);
        parent::__construct($config);

        $this->createRow()->copyFile(VPS_PATH.'/images/information.png', 'foo', 'png', 'image/png');
    }
}
