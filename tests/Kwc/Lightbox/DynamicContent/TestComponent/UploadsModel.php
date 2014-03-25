<?php
class Kwc_Lightbox_DynamicContent_TestComponent_UploadsModel extends Kwf_Test_Uploads_Model
{
    public function __construct($config = array())
    {
        parent::__construct($config);

        $this->createRow()->copyFile(KWF_PATH.'/images/devices/macBook.jpg', 'foo', 'jpg', 'image/jpg');
    }
}
