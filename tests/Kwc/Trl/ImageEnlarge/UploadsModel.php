<?php
class Kwc_Trl_ImageEnlarge_UploadsModel extends Kwf_Test_Uploads_Model
{
    public function __construct($config = array())
    {
        parent::__construct($config);

        $this->createRow(array('id'=>'1'))->copyFile(dirname(__FILE__).'/1.jpg', '1', 'jpg', 'image/jpeg');
        $this->createRow(array('id'=>'2'))->copyFile(dirname(__FILE__).'/2.jpg', '2', 'jpg', 'image/jpeg');
        $this->createRow(array('id'=>'3'))->copyFile(dirname(__FILE__).'/3.jpg', '3', 'jpg', 'image/jpeg');
        $this->createRow(array('id'=>'4'))->save();
        $this->createRow(array('id'=>'5'))->copyFile(dirname(__FILE__).'/5.jpg', '5', 'jpg', 'image/jpeg');
        $this->createRow(array('id'=>'6'))->copyFile(dirname(__FILE__).'/6.jpg', '6', 'jpg', 'image/jpeg');
    }
}
