<?php
class Kwc_Basic_DownloadTag_UploadsModel extends Kwf_Test_Uploads_Model
{
    public $uploadId1;
    public function __construct($config = array())
    {
        parent::__construct($config);

        $r = $this->createRow();
        $r->copyFile(KWF_PATH.'/images/information.png', 'foo', 'png', 'image/png');
        $this->uploadId1 = $r->id;
    }
}
