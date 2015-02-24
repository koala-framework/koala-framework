<?php
class Kwc_ImageResponsive_MediaOutput_Image_UploadsModel extends Kwf_Test_Uploads_Model
{
    public $uploadId1;
    public $uploadId2;
    public $uploadId3;
    public function __construct($config = array())
    {
        parent::__construct($config);

        $r = $this->createRow();
        $r->copyFile(KWF_PATH.'/images/errorWarning.jpg', 'foo', 'jpg', 'image/jpg');
        $this->uploadId1 = $r->id;

        $r = $this->createRow();
        $r->copyFile(KWF_PATH.'/images/vividplanet.gif', 'foo', 'gif', 'image/gif');
        $this->uploadId2 = $r->id;

        $r = $this->createRow();
        $r->copyFile(KWF_PATH.'/images/devices/macBook.jpg', 'foo', 'jpg', 'image/jpg');
        $this->uploadId3 = $r->id;
    }
}
