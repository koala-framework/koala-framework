<?php
class Kwc_Basic_DownloadTagBehindLogin_UploadsModel extends Kwf_Test_Uploads_Model
{
    public function __construct($config = array())
    {
        $config = array();
        $config['uploadDir'] = 'temp/tests/DownloadTagBehindLogin/uploads';
        if (!file_exists($config['uploadDir'])) mkdir($config['uploadDir'], 0777, true);
        parent::__construct($config);

        $this->createRow()->copyFile(KWF_PATH.'/images/information.png', 'foo', 'png', 'image/png');
    }
}
