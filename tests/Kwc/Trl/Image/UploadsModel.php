<?php
class Kwc_Trl_Image_UploadsModel extends Kwf_Test_Uploads_Model
{
    public function __construct($config = array())
    {
        parent::__construct($config);

        $this->createRow(array('id'=>'1'))->copyFile(dirname(__FILE__).'/1.jpg', '1', 'jpg', 'image/jpeg');
        $this->createRow(array('id'=>'2'))->copyFile(dirname(__FILE__).'/2.jpg', '2', 'jpg', 'image/jpeg');
    }
}
