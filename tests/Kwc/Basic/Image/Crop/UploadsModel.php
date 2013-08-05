<?php
class Kwc_Basic_Image_Crop_UploadsModel extends Kwf_Test_Uploads_Model
{
    public function __construct($config = array())
    {
        parent::__construct($config);

        $this->createRow()->copyFile(KWF_PATH.'/images/colorpicker/map-blue-max.png', 'foo2', 'png', 'image/png');
    }
}
