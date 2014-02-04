<?php
class Kwc_Abstract_Image_UploadsModel extends Kwf_Test_Uploads_Model
{
    public function __construct($config = array())
    {
        parent::__construct($config);

        $this->createRow()->copyFile(KWF_PATH.'/images/errorWarning.jpg', 'foo', 'jpg', 'image/jpg');
        $this->createRow()->copyFile(KWF_PATH.'/images/vividplanet.gif', 'foo', 'gif', 'image/gif');
    }
}
