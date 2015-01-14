<?php
class Kwc_Basic_Text_Download_UploadsModel extends Kwf_Test_Uploads_Model
{
    public function __construct($config = array())
    {
        parent::__construct($config);

        $this->createRow(array(
            'id' => 'b7715975-0252-4d31-ae9c-589a5f11620a'
        ))->copyFile(KWF_PATH.'/images/information.png', 'foo', 'png', 'image/png');
    }
}
