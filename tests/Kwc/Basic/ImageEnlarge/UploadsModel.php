<?php
class Kwc_Basic_ImageEnlarge_UploadsModel extends Kwf_Test_Uploads_Model
{
    public function __construct($config = array())
    {
        parent::__construct($config);

        $this->createRow(array('id'=>'1'))->copyFile(KWF_PATH.'/images/information.png', 'foo', 'png', 'image/png');
        $this->createRow(array('id'=>'2'))->copyFile(KWF_PATH.'/images/vividplanet.gif', 'foo', 'gif', 'image/gif');
        $this->createRow(array('id'=>'3'))->copyFile(KWF_PATH.'/images/information.png', 'foo', 'png', 'image/png');
        $this->createRow(array('id'=>'4'))->copyFile(KWF_PATH.'/tests/images/koala.jpg', 'koala', 'jpg', 'image/jpeg');
        $this->createRow(array('id'=>'5'))->copyFile(KWF_PATH.'/tests/images/koalaSmaller.jpg', 'koala', 'jpg', 'image/jpeg');
    }

}
