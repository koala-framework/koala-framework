<?php
class Kwc_Basic_Image_UploadsModel extends Kwf_Test_Uploads_Model
{
    public function __construct($config = array())
    {
        parent::__construct($config);

        $this->createRow(array('id'=>'1'))->copyFile(KWF_PATH.'/images/information.png', 'foo', 'png', 'image/png');
        $this->createRow(array('id'=>'2'))->copyFile(KWF_PATH.'/images/errorWarning.jpg', 'errorWarning', 'jpg', 'image/jpg');
        $this->createRow(array('id'=>'3'))->copyFile(KWF_PATH.'/images/vividplanet.gif', 'vividplanet', 'gif', 'image/gif');
    }
}
