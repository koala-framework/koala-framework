<?php
class Kwc_Basic_Image_UploadsModel extends Kwf_Test_Uploads_Model
{
    public function __construct($config = array())
    {
        parent::__construct($config);

        $this->createRow()->copyFile(KWF_PATH.'/images/information.png', 'foo', 'png', 'image/png');
        $this->createRow()->copyFile(KWF_PATH.'/images/errorWarning.jpg', 'errorWarning', 'jpg', 'image/jpg');
        $this->createRow()->copyFile(KWF_PATH.'/images/vividplanet.gif', 'vividplanet', 'gif', 'image/gif');
    }
}
