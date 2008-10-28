<?php
class Vps_Uploads_TestModel extends Vps_Uploads_Model
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
    }

    public function __destruct()
    {
        $dir = $this->getUploadDir();
        if (substr($dir, 0, 4)=='/tmp') {
            system('rm -r '.$dir);
        }
    }
}
