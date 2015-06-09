<?php
class Kwc_Basic_Image_CacheFullWidth_UploadsModel extends Kwf_Test_Uploads_Model
{
    public function __construct($config = array())
    {
        parent::__construct($config);

        $this->createRow(array('id'=>'1'))->copyFile(KWF_PATH.'/images/information.png', 'foo', 'png', 'image/png');
    }
}
