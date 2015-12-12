<?php
class Kwc_List_GalleryBasic_ImageEnlarge_UploadsModel extends Kwf_Test_Uploads_Model
{
    public function __construct($config = array())
    {
        parent::__construct($config);

        $this->createRow(array('id'=>'1'))->copyFile(KWF_PATH.'/images/devices/iPadPortrait.jpg', 'foo2', 'jpg', 'image/jpg');
    }
}
