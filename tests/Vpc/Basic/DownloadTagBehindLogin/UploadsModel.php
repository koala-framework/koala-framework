<?php
class Vpc_Basic_DownloadTagBehindLogin_UploadsModel extends Vps_Test_Uploads_Model
{
    public function __construct($config = array())
    {
        $config = array();
        $config['uploadDir'] = 'application/temp/tests/DownloadTagBehindLogin/uploads';
        if (!file_exists($config['uploadDir'])) mkdir($config['uploadDir'], 0777, true);
        parent::__construct($config);

        $this->createRow()->copyFile(VPS_PATH.'/images/information.png', 'foo', 'png', 'image/png');
    }
}
