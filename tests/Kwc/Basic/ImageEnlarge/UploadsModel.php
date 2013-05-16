<?php
class Kwc_Basic_ImageEnlarge_UploadsModel extends Kwf_Test_Uploads_Model
{
    public function __construct($config = array())
    {
        parent::__construct($config);

        $this->createRow()->copyFile(KWF_PATH.'/images/information.png', 'foo', 'png', 'image/png');
        $this->createRow()->copyFile(KWF_PATH.'/images/vividplanet.gif', 'foo', 'gif', 'image/gif');
        $this->createRow()->copyFile(KWF_PATH.'/images/information.png', 'foo', 'png', 'image/png');
        $this->createRow()->copyFile(KWF_PATH.'/tests/images/koala.jpg', 'koala', 'jpg', 'image/jpeg');
    }

}
