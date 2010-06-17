<?php
class Vpc_Trl_ImageEnlarge_UploadsModel extends Vps_Test_Uploads_Model
{
    public function __construct($config = array())
    {
        parent::__construct($config);

        $this->createRow()->copyFile(dirname(__FILE__).'/1.jpg', '1', 'jpg', 'image/jpeg');
        $this->createRow()->copyFile(dirname(__FILE__).'/2.jpg', '2', 'jpg', 'image/jpeg');
        $this->createRow()->save();
        $this->createRow()->save();
        $this->createRow()->copyFile(dirname(__FILE__).'/5.jpg', '5', 'jpg', 'image/jpeg');
        $this->createRow()->copyFile(dirname(__FILE__).'/6.jpg', '6', 'jpg', 'image/jpeg');
    }
}
