<?php
class Kwc_Basic_Text_Image_UploadsModel extends Kwf_Test_Uploads_Model
{
    public function __construct($config = array())
    {
        parent::__construct($config);

        $this->createRow(array(
            'id' => 'c1f100f2-8967-4d03-8773-dbe3b43f3955'
        ))->copyFile(KWF_PATH.'/images/information.png', 'foo', 'png', 'image/png');
    }
}
